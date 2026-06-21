<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\GradingLog;
use App\Models\Grade;
use App\Models\QuestionBank;
use App\Models\Result;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function createExam(array $data): Exam
    {
        $data['teacher_id'] = Auth::user()->teacher?->id;
        return Exam::create($data);
    }

    public function updateExam(Exam $exam, array $data): Exam
    {
        $exam->update($data);
        return $exam->fresh();
    }

    public function publishExam(Exam $exam): void
    {
        $exam->update([
            'is_published' => true,
            'status' => 'published',
        ]);
    }

    public function archiveExam(Exam $exam): void
    {
        $exam->update(['status' => 'archived']);
    }

    public function addQuestions(Exam $exam, array $questionIds, array $marks = []): void
    {
        $order = $exam->questions()->count();
        $syncData = [];
        foreach ($questionIds as $index => $qId) {
            $syncData[$qId] = [
                'marks' => $marks[$qId] ?? null,
                'order' => $order + $index + 1,
            ];
        }
        $exam->questions()->attach($syncData);
    }

    public function calculateTotalMarks(Exam $exam): float
    {
        return (float) $exam->questions()->sum('exam_questions.marks');
    }

    public function startExam(Exam $exam, int $studentId): ExamAttempt
    {
        $attempt = ExamAttempt::firstOrCreate(
            ['student_id' => $studentId, 'exam_id' => $exam->id],
            ['status' => 'in_progress', 'started_at' => now()]
        );

        if ($attempt->status === 'not_started') {
            $attempt->update(['status' => 'in_progress', 'started_at' => now()]);
        }

        return $attempt;
    }

    public function saveAnswer(ExamAttempt $attempt, int $questionId, ?int $optionId, ?string $text): StudentAnswer
    {
        $answer = StudentAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $questionId],
            ['selected_option_id' => $optionId, 'answer_text' => $text]
        );

        if ($optionId) {
            $option = $answer->question->options()->find($optionId);
            if ($option) {
                $answer->update(['is_correct' => $option->is_correct]);
            }
        }

        return $answer;
    }

    public function submitExam(ExamAttempt $attempt): ExamAttempt
    {
        $exam = $attempt->exam;
        $autoGrade = $exam->show_results_immediately;

        DB::transaction(function () use ($attempt, $exam, $autoGrade) {
            $attempt->update([
                'submitted_at' => now(),
                'status' => $autoGrade ? 'graded' : 'submitted',
            ]);

            if ($autoGrade) {
                $this->autoGradeAttempt($attempt, $exam);
            }
        });

        return $attempt->fresh();
    }

    public function autoGradeAttempt(ExamAttempt $attempt, Exam $exam): void
    {
        $totalScore = 0;
        $totalPossible = $exam->questions()->sum('exam_questions.marks');

        foreach ($attempt->answers as $answer) {
            $question = $answer->question;
            $pivotMarks = $exam->questions()->find($question->id)?->pivot?->marks ?? $question->default_marks;

            if ($answer->selected_option_id) {
                $option = $question->options()->find($answer->selected_option_id);
                if ($option && $option->is_correct) {
                    $marksObtained = (float) $pivotMarks;
                    $answer->update(['is_correct' => true, 'marks_obtained' => $marksObtained]);
                    $totalScore += $marksObtained;
                } else {
                    $marksObtained = $exam->negative_marking ? -1 * (float) $exam->negative_mark_value : 0;
                    $answer->update(['is_correct' => false, 'marks_obtained' => $marksObtained]);
                    $totalScore += $marksObtained;
                }
            } elseif ($answer->answer_text) {
                // Text answers need manual grading - skip auto-grade
                $answer->update(['marks_obtained' => 0]);
            }
        }

        $totalScore = max(0, $totalScore);
        $percentage = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;
        $resultStatus = $percentage >= $exam->pass_mark ? 'pass' : 'fail';

        $attempt->update([
            'total_score' => $totalScore,
            'percentage_score' => $percentage,
            'result_status' => $resultStatus,
            'graded_at' => now(),
            'graded_by' => Auth::user()->teacher?->id,
        ]);
    }

    public function gradeQuestion(StudentAnswer $answer, float $marks, ?string $feedback, int $graderId): void
    {
        $previousMarks = $answer->marks_obtained;

        GradingLog::create([
            'attempt_id' => $answer->attempt_id,
            'question_id' => $answer->question_id,
            'grader_id' => $graderId,
            'marks_awarded' => $marks,
            'marks_previous' => $previousMarks,
            'comment' => $feedback,
        ]);

        $answer->update([
            'marks_obtained' => $marks,
            'feedback' => $feedback,
            'is_correct' => $marks > 0,
        ]);
    }

    public function finalizeGrading(ExamAttempt $attempt): void
    {
        $exam = $attempt->exam;
        $totalScore = $attempt->answers()->sum('marks_obtained') ?? 0;
        $totalPossible = $exam->questions()->sum('exam_questions.marks');
        $percentage = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;
        $resultStatus = $percentage >= $exam->pass_mark ? 'pass' : 'fail';

        $attempt->update([
            'total_score' => $totalScore,
            'percentage_score' => $percentage,
            'result_status' => $resultStatus,
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => Auth::user()->teacher?->id,
        ]);
    }

    public function publishResults(Exam $exam): void
    {
        $exam->update(['results_published' => true]);

        foreach ($exam->attempts()->where('status', 'graded')->cursor() as $attempt) {
            $grade = Grade::where('min_score', '<=', $attempt->percentage_score ?? 0)
                ->where('max_score', '>=', $attempt->percentage_score ?? 0)
                ->first();

            Result::updateOrCreate(
                [
                    'student_id' => $attempt->student_id,
                    'exam_id' => $exam->id,
                    'subject_id' => $exam->subject_id,
                ],
                [
                    'score' => $attempt->percentage_score,
                    'grade_id' => $grade?->id,
                    'remarks' => $attempt->result_status === 'pass' ? 'Passed' : 'Failed',
                    'teacher_id' => $exam->teacher_id,
                    'graded_by' => $attempt->graded_by,
                    'is_published' => true,
                ]
            );
        }
    }

    public function getStudentUpcomingExams(int $studentId)
    {
        return Exam::where('is_published', true)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '>=', now());
            })
            ->whereHas('class', function ($q) use ($studentId) {
                $q->whereHas('students', fn($sq) => $sq->where('student_id', $studentId));
            })
            ->with('subject', 'class')
            ->orderBy('start_date')
            ->get();
    }

    public function getStudentAvailableExams(int $studentId)
    {
        return Exam::where('is_published', true)
            ->where('exam_mode', 'online')
            ->where('status', 'published')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('class', function ($q) use ($studentId) {
                $q->whereHas('students', fn($sq) => $sq->where('student_id', $studentId));
            })
            ->with('subject', 'class')
            ->get();
    }

    public function getExamStats(): array
    {
        return [
            'total_exams' => Exam::count(),
            'published_exams' => Exam::where('is_published', true)->count(),
            'online_exams' => Exam::where('exam_mode', 'online')->count(),
            'upcoming_exams' => Exam::where('is_published', true)
                ->where('start_date', '>=', now())->count(),
            'total_attempts' => ExamAttempt::count(),
            'graded_attempts' => ExamAttempt::where('status', 'graded')->count(),
            'pass_count' => ExamAttempt::where('result_status', 'pass')->count(),
            'fail_count' => ExamAttempt::where('result_status', 'fail')->count(),
            'total_questions' => QuestionBank::count(),
            'active_questions' => QuestionBank::where('is_active', true)->count(),
        ];
    }
}
