<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $results = Result::with(['student.user', 'subject', 'exam', 'grade'])->latest()->paginate(15);

        return view('results.index', compact('results'));
    }

    public function create()
    {
        $students = Student::with('user')->orderBy('id')->get();
        $subjects = Subject::orderBy('name')->get();
        $exams = Exam::orderBy('name')->get();

        return view('results.create', compact('students', 'subjects', 'exams'));
    }

    public function store(Request $request)
    {
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

        Result::create([
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id'],
            'exam_id' => $data['exam_id'],
            'score' => $data['score'],
            'grade_id' => $grade?->id,
            'remarks' => $data['remarks'] ?? null,
        ]);

        return redirect()->route('results.index')->with('success', 'Result saved successfully.');
    }

    public function edit(Result $result)
    {
        $students = Student::with('user')->orderBy('id')->get();
        $subjects = Subject::orderBy('name')->get();
        $exams = Exam::orderBy('name')->get();

        return view('results.edit', compact('result', 'students', 'subjects', 'exams'));
    }

    public function update(Request $request, Result $result)
    {
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
        $result->delete();

        return redirect()->route('results.index')->with('success', 'Result deleted successfully.');
    }
}
