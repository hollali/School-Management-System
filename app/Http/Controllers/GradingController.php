<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradingController extends Controller
{
    protected ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index()
    {
        $user = Auth::user();
        $query = ExamAttempt::with('exam.subject', 'student.user')
            ->where('status', 'submitted');

        if ($user->hasRole('Teacher')) {
            $query->whereHas('exam', fn($q) => $q->where('teacher_id', $user->teacher?->id));
        }

        $pendingGrading = $query->latest()->paginate(15);

        $gradedQuery = ExamAttempt::with('exam.subject', 'student.user')
            ->where('status', 'graded');

        if ($user->hasRole('Teacher')) {
            $gradedQuery->whereHas('exam', fn($q) => $q->where('teacher_id', $user->teacher?->id));
        }

        $graded = $gradedQuery->latest()->paginate(15, ['*'], 'graded_page');

        return view('grading.index', compact('pendingGrading', 'graded'));
    }

    public function grade(ExamAttempt $attempt)
    {
        $attempt->load([
            'exam.subject',
            'exam.questions' => fn($q) => $q->orderBy('exam_questions.order'),
            'answers.question.options',
            'answers.selectedOption',
            'student.user',
        ]);

        return view('grading.grade', compact('attempt'));
    }

    public function saveGrade(Request $request, ExamAttempt $attempt)
    {
        $data = $request->validate([
            'answers' => 'required|array',
            'answers.*.marks' => 'required|numeric|min:0',
            'answers.*.feedback' => 'nullable|string',
        ]);

        $graderId = Auth::user()->teacher?->id;

        foreach ($data['answers'] as $answerId => $gradeData) {
            $answer = StudentAnswer::findOrFail($answerId);
            $this->examService->gradeQuestion(
                $answer,
                $gradeData['marks'],
                $gradeData['feedback'] ?? null,
                $graderId
            );
        }

        return response()->json(['status' => 'saved']);
    }

    public function finalize(ExamAttempt $attempt)
    {
        $this->examService->finalizeGrading($attempt);
        return redirect()->route('grading.index')->with('success', 'Grading finalized successfully.');
    }

    public function examSubmissions(Exam $exam)
    {
        $attempts = ExamAttempt::with('student.user')
            ->where('exam_id', $exam->id)
            ->where('status', 'submitted')
            ->get();

        return view('grading.exam-submissions', compact('exam', 'attempts'));
    }
}
