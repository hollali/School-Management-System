<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\AcademicTerm;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Services\ExamService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    use AuthorizesRequests;

    protected ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Teacher')) {
            $exams = Exam::with('subject', 'class', 'teacher.user')
                ->where('teacher_id', $user->teacher?->id)
                ->latest()
                ->paginate(15);
        } elseif ($user->hasRole('Student')) {
            $student = $user->student;
            $classIds = $student->classes->pluck('id');
            $exams = Exam::with('subject', 'class', 'teacher.user')
                ->whereIn('class_id', $classIds)
                ->latest()
                ->paginate(15);
        } elseif ($user->hasRole('Admin')) {
            $exams = Exam::with('subject', 'class', 'teacher.user')->latest()->paginate(15);
        } else {
            $exams = Exam::with('subject', 'class', 'teacher.user')->latest()->paginate(15);
        }

        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $academicTerms = AcademicTerm::orderByDesc('start_date')->get();

        return view('exams.index', compact('exams', 'subjects', 'classes', 'academicTerms'));
    }

    public function create()
    {
        $this->authorize('create', Exam::class);
        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $academicTerms = AcademicTerm::orderByDesc('start_date')->get();
        return view('exams.create', compact('subjects', 'classes', 'academicTerms'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'total_marks' => 'nullable|numeric|min:0',
            'pass_mark' => 'nullable|numeric|min:0',
            'max_attempts' => 'nullable|integer|min:1|max:100',
            'exam_mode' => 'nullable|in:online,offline',
            'exam_date' => 'nullable|date',
            'term' => 'nullable|string|max:255',
            'academic_year' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'negative_marking' => 'nullable|boolean',
            'negative_mark_value' => 'nullable|numeric|min:0',
            'fullscreen_required' => 'nullable|boolean',
            'tab_switch_detection' => 'nullable|boolean',
            'copy_paste_disabled' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
        ]);

        $data['shuffle_questions'] = $request->boolean('shuffle_questions');
        $data['shuffle_options'] = $request->boolean('shuffle_options');
        $data['negative_marking'] = $request->boolean('negative_marking');
        $data['fullscreen_required'] = $request->boolean('fullscreen_required');
        $data['tab_switch_detection'] = $request->boolean('tab_switch_detection');
        $data['copy_paste_disabled'] = $request->boolean('copy_paste_disabled');
        $data['show_results_immediately'] = $request->boolean('show_results_immediately');

        $exam = $this->examService->createExam($data);

        ActivityLogger::log('exam-created', 'Exam', $exam->id, "Created exam: {$exam->name}");

        return redirect()->route('exams.show', $exam)->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);

        $exam->load('subject', 'class', 'teacher.user', 'academicTerm', 'questions.options', 'schedules');

        $attempts = null;
        if (Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('Admin')) {
            $attempts = $exam->attempts()->with('student.user')->get();
        }

        $questionBank = QuestionBank::where('subject_id', $exam->subject_id)
            ->where('is_active', true)
            ->get();

        return view('exams.show', compact('exam', 'attempts', 'questionBank'));
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);
        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $academicTerms = AcademicTerm::orderByDesc('start_date')->get();
        return view('exams.edit', compact('exam', 'subjects', 'classes', 'academicTerms'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            'academic_term_id' => 'nullable|exists:academic_terms,id',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'total_marks' => 'nullable|numeric|min:0',
            'pass_mark' => 'nullable|numeric|min:0',
            'max_attempts' => 'nullable|integer|min:1|max:100',
            'exam_mode' => 'nullable|in:online,offline',
            'exam_date' => 'nullable|date',
            'term' => 'nullable|string|max:255',
            'academic_year' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'negative_marking' => 'nullable|boolean',
            'negative_mark_value' => 'nullable|numeric|min:0',
            'fullscreen_required' => 'nullable|boolean',
            'tab_switch_detection' => 'nullable|boolean',
            'copy_paste_disabled' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
        ]);

        $data['shuffle_questions'] = $request->boolean('shuffle_questions');
        $data['shuffle_options'] = $request->boolean('shuffle_options');
        $data['negative_marking'] = $request->boolean('negative_marking');
        $data['fullscreen_required'] = $request->boolean('fullscreen_required');
        $data['tab_switch_detection'] = $request->boolean('tab_switch_detection');
        $data['copy_paste_disabled'] = $request->boolean('copy_paste_disabled');
        $data['show_results_immediately'] = $request->boolean('show_results_immediately');

        $this->examService->updateExam($exam, $data);

        ActivityLogger::log('exam-updated', 'Exam', $exam->id, "Updated exam: {$exam->name}");

        return redirect()->route('exams.show', $exam)->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);

        ActivityLogger::log('exam-deleted', 'Exam', $exam->id, "Deleted exam: {$exam->name}");
        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function publish(Exam $exam)
    {
        $this->authorize('update', $exam);
        $this->examService->publishExam($exam);

        ActivityLogger::log('exam-published', 'Exam', $exam->id, "Published exam: {$exam->name}");

        return redirect()->route('exams.show', $exam)->with('success', 'Exam published successfully.');
    }

    public function addQuestions(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $data = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:question_bank,id',
            'marks' => 'nullable|array',
            'marks.*' => 'nullable|numeric|min:0',
        ]);

        $this->examService->addQuestions($exam, $data['question_ids'], $data['marks'] ?? []);

        return redirect()->route('exams.show', $exam)->with('success', 'Questions added successfully.');
    }

    public function removeQuestion(Exam $exam, $questionId)
    {
        $this->authorize('update', $exam);
        $exam->questions()->detach($questionId);
        return redirect()->route('exams.show', $exam)->with('success', 'Question removed successfully.');
    }

    public function publishResults(Exam $exam)
    {
        $this->authorize('update', $exam);
        $this->examService->publishResults($exam);

        ActivityLogger::log('results-published', 'Exam', $exam->id, "Published results for exam: {$exam->name}");

        return redirect()->route('exams.show', $exam)->with('success', 'Results published successfully.');
    }
}
