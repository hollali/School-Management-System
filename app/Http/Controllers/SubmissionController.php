<?php

namespace App\Http\Controllers;

use App\Events\SubmissionRejected;
use App\Events\SubmissionRetracted;
use App\Events\SubmissionSubmitted;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Submission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Submission::class);

        $user = Auth::user();
        $query = Submission::with(['assignment', 'student.user', 'feedback']);

        if ($user->hasRole('Teacher')) {
            $query->whereHas('assignment', function ($q) use ($user) {
                $q->where('teacher_id', $user->teacher?->id);
            });
        } elseif ($user->hasRole('Student')) {
            $query->where('student_id', $user->student?->id);
        }

        if ($assignmentId = request('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $submissions = $query->latest()->paginate(15);

        $assignments = Assignment::when($user->hasRole('Teacher'), function ($q) use ($user) {
            $q->where('teacher_id', $user->teacher?->id);
        })->latest()->get();

        $students = collect();
        if ($user->hasRole('Admin')) {
            $students = Student::with('user')->latest()->get();
        }

        return view('submissions.index', compact('submissions', 'assignments', 'students'));
    }

    public function create()
    {
        return redirect()->route('submissions.index');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Submission::class);

        $user = Auth::user();

        $data = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'content'       => ['nullable', 'string'],
            'attachment'    => ['nullable', 'file', 'max:2048', 'mimes:txt,pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,bmp,svg,zip,mp4,avi,mov,wmv,webm,mkv,flv'],
        ], [
            'attachment.max' => 'The file must not be larger than 2MB.',
            'attachment.mimes' => 'The file type is not supported.',
        ]);

        $assignment = Assignment::findOrFail($data['assignment_id']);
        $student = $user->student;

        if (!$student) {
            return back()->withErrors(['student' => 'Only students can submit assignments.'])->withInput();
        }

        $inClass = $student->classes()->where('classes.id', $assignment->class_id)->exists();
        if (!$inClass) {
            return back()->withErrors(['assignment_id' => 'You can only submit to assignments for your class.'])->withInput();
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileHash = md5_file($file->getRealPath());

            $duplicate = Submission::where('assignment_id', $data['assignment_id'])
                ->where('file_hash', $fileHash)
                ->exists();

            if ($duplicate) {
                return back()->withErrors(['attachment' => 'This file has already been submitted for this assignment.'])->withInput();
            }

            $data['file_hash'] = $fileHash;
            $data['attachment_path'] = $file->store('submissions', 'public');
        }

        unset($data['attachment']);
        $data['student_id'] = $student->id;
        $data['submitted_at'] = now();
        $data['status'] = 'submitted';

        $existing = Submission::where('assignment_id', $data['assignment_id'])
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            $existing->update($data);
            $submission = $existing;
        } else {
            $submission = Submission::create($data);
        }

        event(new SubmissionSubmitted($submission));

        return redirect()->route('submissions.index')
            ->with('success', 'Submission created successfully.');
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['assignment', 'student.user', 'feedback.teacher.user']);

        return view('submissions.show', compact('submission'));
    }

    public function edit(Submission $submission)
    {
        return redirect()->route('submissions.index');
    }

    public function update(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $data = $request->validate([
            'content'       => ['nullable', 'string'],
            'attachment'    => ['nullable', 'file', 'max:2048', 'mimes:txt,pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,bmp,svg,zip,mp4,avi,mov,wmv,webm,mkv,flv'],
        ], [
            'attachment.max' => 'The file must not be larger than 2MB.',
            'attachment.mimes' => 'The file type is not supported.',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileHash = md5_file($file->getRealPath());

            $duplicate = Submission::where('assignment_id', $submission->assignment_id)
                ->where('file_hash', $fileHash)
                ->where('id', '!=', $submission->id)
                ->exists();

            if ($duplicate) {
                return back()->withErrors(['attachment' => 'This file has already been submitted for this assignment.'])->withInput();
            }

            if ($submission->attachment_path) {
                Storage::disk('public')->delete($submission->attachment_path);
            }

            $data['file_hash'] = $fileHash;
            $data['attachment_path'] = $file->store('submissions', 'public');
            $data['submitted_at'] = now();
            $data['status'] = 'submitted';
            $data['rejection_reason'] = null;
        }

        unset($data['attachment']);

        $submission->update($data);

        return redirect()->route('submissions.index')
            ->with('success', 'Submission updated successfully.');
    }

    public function destroy(Submission $submission)
    {
        $this->authorize('delete', $submission);

        if ($submission->attachment_path) {
            Storage::disk('public')->delete($submission->attachment_path);
        }

        $submission->delete();

        return redirect()->route('submissions.index')
            ->with('success', 'Submission deleted successfully.');
    }

    public function retract(Submission $submission)
    {
        $this->authorize('retract', $submission);

        $submission->update([
            'status' => 'retracted',
            'submitted_at' => null,
        ]);

        if ($submission->attachment_path) {
            Storage::disk('public')->delete($submission->attachment_path);
            $submission->update([
                'attachment_path' => null,
                'file_hash' => null,
            ]);
        }

        event(new SubmissionRetracted($submission));

        return redirect()->back()
            ->with('success', 'Submission withdrawn successfully.');
    }

    public function reject(Request $request, Submission $submission)
    {
        $this->authorize('reject', $submission);

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10'],
        ]);

        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'],
        ]);

        event(new SubmissionRejected($submission, $data['rejection_reason']));

        return redirect()->back()
            ->with('success', 'Submission rejected successfully.');
    }
}
