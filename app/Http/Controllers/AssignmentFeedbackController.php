<?php

namespace App\Http\Controllers;

use App\Models\AssignmentFeedback;
use App\Models\Submission;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AssignmentFeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = AssignmentFeedback::with(['submission.assignment', 'teacher.user'])
            ->latest()
            ->paginate(15);

        $submissions = Submission::with('assignment', 'student.user')
            ->whereDoesntHave('feedback')
            ->get();

        $teachers = Teacher::with('user')->latest()->get();

        return view('assignment_feedback.index', compact('feedbacks', 'submissions', 'teachers'));
    }

    public function create()
    {
        return redirect()->route('assignment-feedback.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'submission_id' => ['required', 'exists:submissions,id'],
            'teacher_id'    => ['required', 'exists:teachers,id'],
            'comments'      => ['nullable', 'string'],
            'score'         => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        AssignmentFeedback::create($data);

        return redirect()->route('assignment-feedback.index')
            ->with('success', 'Feedback created successfully.');
    }

    public function show(AssignmentFeedback $assignmentFeedback)
    {
        $assignmentFeedback->load(['submission.student.user', 'submission.assignment', 'teacher.user']);

        return view('assignment_feedback.show', compact('assignmentFeedback'));
    }

    public function edit(AssignmentFeedback $assignmentFeedback)
    {
        return redirect()->route('assignment-feedback.index');
    }

    public function update(Request $request, AssignmentFeedback $assignmentFeedback)
    {
        $data = $request->validate([
            'submission_id' => ['required', 'exists:submissions,id'],
            'teacher_id'    => ['required', 'exists:teachers,id'],
            'comments'      => ['nullable', 'string'],
            'score'         => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $assignmentFeedback->update($data);

        return redirect()->route('assignment-feedback.index')
            ->with('success', 'Feedback updated successfully.');
    }

    public function destroy(AssignmentFeedback $assignmentFeedback)
    {
        $assignmentFeedback->delete();

        return redirect()->route('assignment-feedback.index')
            ->with('success', 'Feedback deleted successfully.');
    }
}
