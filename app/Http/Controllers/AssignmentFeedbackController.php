<?php

namespace App\Http\Controllers;

use App\Events\AssignmentGraded;
use App\Models\AssignmentFeedback;
use App\Models\Submission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentFeedbackController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', AssignmentFeedback::class);

        $user = Auth::user();
        $query = AssignmentFeedback::with(['submission.student.user', 'submission.assignment', 'teacher.user']);

        if ($user->hasRole('Teacher')) {
            $query->whereHas('submission.assignment', function ($q) use ($user) {
                $q->where('teacher_id', $user->teacher?->id);
            });
        } elseif ($user->hasRole('Student')) {
            $query->whereHas('submission', function ($q) use ($user) {
                $q->where('student_id', $user->student?->id);
            });
        }

        $feedbacks = $query->latest()->paginate(15);

        $submissions = Submission::with('assignment', 'student.user')
            ->whereDoesntHave('feedback')
            ->when($user->hasRole('Teacher'), function ($q) use ($user) {
                $q->whereHas('assignment', function ($aq) use ($user) {
                    $aq->where('teacher_id', $user->teacher?->id);
                });
            })
            ->get();

        $teachers = collect();
        if ($user->hasRole('Admin')) {
            $teachers = \App\Models\Teacher::with('user')->latest()->get();
        }

        return view('assignment_feedback.index', compact('feedbacks', 'submissions', 'teachers'));
    }

    public function create()
    {
        return redirect()->route('assignment-feedback.index');
    }

    public function store(Request $request)
    {
        $this->authorize('create', AssignmentFeedback::class);

        $user = Auth::user();
        $teacher = $user->teacher;

        $data = $request->validate([
            'submission_id' => ['required', 'exists:submissions,id'],
            'comments'      => ['nullable', 'string'],
            'score'         => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $submission = Submission::findOrFail($data['submission_id']);

        if ($submission->assignment->teacher_id !== $teacher->id) {
            return back()->withErrors(['submission_id' => 'You can only grade submissions for your own assignments.'])->withInput();
        }

        $data['teacher_id'] = $teacher->id;

        $feedback = AssignmentFeedback::create($data);

        $submission->update(['status' => 'graded']);

        event(new AssignmentGraded($feedback));

        return redirect()->route('assignment-feedback.index')
            ->with('success', 'Assignment assessed successfully. Grade and feedback saved.');
    }

    public function show(AssignmentFeedback $assignmentFeedback)
    {
        $this->authorize('view', $assignmentFeedback);

        $assignmentFeedback->load(['submission.student.user', 'submission.assignment', 'teacher.user']);

        return view('assignment_feedback.show', compact('assignmentFeedback'));
    }

    public function edit(AssignmentFeedback $assignmentFeedback)
    {
        return redirect()->route('assignment-feedback.index');
    }

    public function update(Request $request, AssignmentFeedback $assignmentFeedback)
    {
        $this->authorize('update', $assignmentFeedback);

        $data = $request->validate([
            'comments'      => ['nullable', 'string'],
            'score'         => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $assignmentFeedback->update($data);

        event(new AssignmentGraded($assignmentFeedback));

        return redirect()->route('assignment-feedback.index')
            ->with('success', 'Grade and feedback updated successfully.');
    }

    public function destroy(AssignmentFeedback $assignmentFeedback)
    {
        $this->authorize('delete', $assignmentFeedback);

        $submission = $assignmentFeedback->submission;
        $assignmentFeedback->delete();
        $submission->update(['status' => 'submitted']);

        return redirect()->route('assignment-feedback.index')
            ->with('success', 'Feedback deleted successfully.');
    }
}
