<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamReportController extends Controller
{
    protected ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index()
    {
        $stats = $this->examService->getExamStats();

        $recentExams = Exam::with('subject', 'teacher.user')
            ->latest()
            ->take(10)
            ->get();

        $topStudents = Student::with('user')
            ->whereHas('examAttempts', fn($q) => $q->where('status', 'graded'))
            ->withAvg('examAttempts as avg_score', 'percentage_score')
            ->orderByDesc('avg_score')
            ->take(10)
            ->get();

        $examPerformance = Exam::withCount([
            'attempts as pass_count' => fn($q) => $q->where('result_status', 'pass'),
            'attempts as fail_count' => fn($q) => $q->where('result_status', 'fail'),
            'attempts as total_attempts',
        ])->get()->filter(fn($e) => $e->total_attempts > 0);

        return view('exam-reports.index', compact('stats', 'recentExams', 'topStudents', 'examPerformance'));
    }

    public function examDetail(Exam $exam)
    {
        $exam->load('subject', 'class', 'teacher.user', 'questions');

        $attempts = ExamAttempt::with('student.user')
            ->where('exam_id', $exam->id)
            ->where('status', 'graded')
            ->get();

        $passCount = $attempts->where('result_status', 'pass')->count();
        $failCount = $attempts->where('result_status', 'fail')->count();
        $avgScore = $attempts->avg('percentage_score');
        $highestScore = $attempts->max('percentage_score');
        $lowestScore = $attempts->min('percentage_score');

        $questionAnalysis = [];
        foreach ($exam->questions as $question) {
            $correctCount = StudentAnswer::where('question_id', $question->id)
                ->whereHas('attempt', fn($q) => $q->where('exam_id', $exam->id))
                ->where('is_correct', true)
                ->count();

            $totalCount = StudentAnswer::where('question_id', $question->id)
                ->whereHas('attempt', fn($q) => $q->where('exam_id', $exam->id))
                ->count();

            $questionAnalysis[] = [
                'question' => $question,
                'correct_count' => $correctCount,
                'total_count' => $totalCount,
                'accuracy' => $totalCount > 0 ? round(($correctCount / $totalCount) * 100, 1) : 0,
            ];
        }

        return view('exam-reports.exam-detail', compact(
            'exam', 'attempts', 'passCount', 'failCount',
            'avgScore', 'highestScore', 'lowestScore', 'questionAnalysis'
        ));
    }

    public function exportCsv(Request $request)
    {
        $examId = $request->exam_id;
        $attempts = ExamAttempt::with('student.user')
            ->where('exam_id', $examId)
            ->where('status', 'graded')
            ->get();

        $filename = "exam-results-{$examId}-" . now()->format('Ymd') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($attempts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student Name', 'Admission Number', 'Score', 'Percentage', 'Result', 'Submitted At', 'Graded At']);

            foreach ($attempts as $attempt) {
                fputcsv($file, [
                    $attempt->student->user->name,
                    $attempt->student->admission_number,
                    $attempt->total_score,
                    $attempt->percentage_score,
                    $attempt->result_status,
                    $attempt->submitted_at?->format('Y-m-d H:i'),
                    $attempt->graded_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
