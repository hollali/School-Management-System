<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $user = Auth::user();
        $query = Result::with(['student.user', 'subject', 'exam', 'grade']);

        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->teacher?->id);
        } elseif ($user->hasRole('Student')) {
            $query->where('student_id', $user->student?->id);
        } elseif ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $query->whereIn('student_id', $studentIds);
        }

        $results = $query->latest()->paginate(15);

        return view('results.index', compact('results'));
    }

    public function create()
    {
        $this->authorize('create', Result::class);

        $teacher = Auth::user()->teacher;
        $classIds = $teacher->classes->pluck('id');
        $students = Student::whereHas('classes', fn($q) => $q->whereIn('class_id', $classIds))
            ->with('user')->orderBy('id')->get();
        $subjects = Subject::orderBy('name')->get();
        $exams = Exam::where('teacher_id', $teacher->id)->orderBy('name')->get();

        return view('results.create', compact('students', 'subjects', 'exams'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Result::class);

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'score' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $grade = Grade::where('min_score', '<=', $data['score'])
            ->where('max_score', '>=', $data['score'])
            ->first();

        $result = Result::create([
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id'],
            'exam_id' => $data['exam_id'],
            'score' => $data['score'],
            'grade_id' => $grade?->id,
            'remarks' => $data['remarks'] ?? null,
            'teacher_id' => Auth::user()->teacher?->id,
        ]);

        ActivityLogger::log('result-created', 'Result', $result->id, "Saved result for student #{$data['student_id']}: {$data['score']}");

        return redirect()->route('results.index')->with('success', 'Result saved successfully.');
    }

    public function edit(Result $result)
    {
        $this->authorize('update', $result);

        $teacher = Auth::user()->teacher;
        $classIds = $teacher->classes->pluck('id');
        $students = Student::whereHas('classes', fn($q) => $q->whereIn('class_id', $classIds))
            ->with('user')->orderBy('id')->get();
        $subjects = Subject::orderBy('name')->get();
        $exams = Exam::where('teacher_id', $teacher->id)->orderBy('name')->get();

        return view('results.edit', compact('result', 'students', 'subjects', 'exams'));
    }

    public function update(Request $request, Result $result)
    {
        $this->authorize('update', $result);

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'score' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $grade = Grade::where('min_score', '<=', $data['score'])
            ->where('max_score', '>=', $data['score'])
            ->first();

        $result->update([
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id'],
            'exam_id' => $data['exam_id'],
            'score' => $data['score'],
            'grade_id' => $grade?->id,
            'remarks' => $data['remarks'] ?? null,
        ]);

        return redirect()->route('results.index')->with('success', 'Result updated successfully.');
    }

    public function destroy(Result $result)
    {
        $this->authorize('delete', $result);

        ActivityLogger::log('result-deleted', 'Result', $result->id, 'Deleted result');
        $result->delete();

        return redirect()->route('results.index')->with('success', 'Result deleted successfully.');
    }
}
