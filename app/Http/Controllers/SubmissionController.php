<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['assignment', 'student.user'])
            ->latest()
            ->paginate(15);

        return view('submissions.index', compact('submissions'));
    }

    public function create()
    {
        $assignments = Assignment::latest()->get();
        $students = Student::with('user')->latest()->get();

        return view('submissions.create', compact('assignments', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'student_id'    => ['required', 'exists:students,id'],
            'content'       => ['nullable', 'string'],
            'attachment'    => ['nullable', 'file', 'max:10240'],
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')
                ->store('submissions', 'public');
        }

        $data['submitted_at'] = now();
        $data['status'] = 'submitted';

        Submission::create($data);

        return redirect()->route('submissions.index')
            ->with('success', 'Submission created successfully.');
    }

    public function show(Submission $submission)
    {
        $submission->load(['assignment', 'student.user', 'feedback.teacher.user']);

        return view('submissions.show', compact('submission'));
    }

    public function edit(Submission $submission)
    {
        $assignments = Assignment::latest()->get();
        $students = Student::with('user')->latest()->get();

        return view('submissions.edit', compact('submission', 'assignments', 'students'));
    }

    public function update(Request $request, Submission $submission)
    {
        $data = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'student_id'    => ['required', 'exists:students,id'],
            'content'       => ['nullable', 'string'],
            'attachment'    => ['nullable', 'file', 'max:10240'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($submission->attachment_path) {
                Storage::disk('public')->delete($submission->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')
                ->store('submissions', 'public');
        }

        $submission->update($data);

        return redirect()->route('submissions.index')
            ->with('success', 'Submission updated successfully.');
    }

    public function destroy(Submission $submission)
    {
        if ($submission->attachment_path) {
            Storage::disk('public')->delete($submission->attachment_path);
        }

        $submission->delete();

        return redirect()->route('submissions.index')
            ->with('success', 'Submission deleted successfully.');
    }
}
