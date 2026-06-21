<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamAttemptController extends Controller
{
    protected ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index()
    {
        $user = Auth::user();
        $student = $user->student;
        $classIds = $student->classes->pluck('id');

        $upcomingExams = $this->examService->getStudentUpcomingExams($student->id);
        $availableExams = $this->examService->getStudentAvailableExams($student->id);
        $attempts = ExamAttempt::with('exam.subject', 'exam.class')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return view('exam-attempts.index', compact('upcomingExams', 'availableExams', 'attempts'));
    }

    public function start(Exam $exam)
    {
        $student = Auth::user()->student;

        if (!$student->classes->pluck('id')->contains($exam->class_id)) {
            return redirect()->route('student.exams')->with('error', 'You are not enrolled in this exam.');
        }

        if (!$exam->isOnline() || !$exam->isActive()) {
            return redirect()->route('student.exams')->with('error', 'This exam is not available for online taking.');
        }

        $existingAttempt = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingAttempt && $existingAttempt->status === 'submitted') {
            return redirect()->route('student.exams')->with('error', 'You have already submitted this exam.');
        }

        if ($existingAttempt && $existingAttempt->status === 'graded') {
            return redirect()->route('student.exams')->with('error', 'You have already completed this exam.');
        }

        $exam->load(['questions.options', 'questions' => function ($q) {
            $q->orderBy('exam_questions.order');
        }]);

        $attempt = $this->examService->startExam($exam, $student->id);

        return view('exam-attempts.take', compact('exam', 'attempt'));
    }

    public function saveAnswer(Request $request, Exam $exam)
    {
        $student = Auth::user()->student;
        $attempt = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $this->examService->saveAnswer(
            $attempt,
            $request->question_id,
            $request->selected_option_id,
            $request->answer_text
        );

        return response()->json(['status' => 'saved']);
    }

    public function submit(Request $request, Exam $exam)
    {
        $student = Auth::user()->student;
        $attempt = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $this->examService->submitExam($attempt);

        return redirect()->route('student.exams.result', $attempt)
            ->with('success', 'Exam submitted successfully.');
    }

    public function result(ExamAttempt $attempt)
    {
        $student = Auth::user()->student;

        if ($attempt->student_id !== $student->id) {
            abort(403);
        }

        $attempt->load('exam.subject', 'exam.questions', 'answers.question', 'answers.selectedOption');

        return view('exam-attempts.result', compact('attempt'));
    }

    public function history()
    {
        $student = Auth::user()->student;
        $attempts = ExamAttempt::with('exam.subject', 'exam.class')
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(15);

        return view('exam-attempts.history', compact('attempts'));
    }
}
