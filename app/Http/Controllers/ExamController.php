<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Exam;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Teacher')) {
            $exams = Exam::where('teacher_id', $user->teacher?->id)->latest()->paginate(15);
        } else {
            $exams = Exam::latest()->paginate(15);
        }

        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        $this->authorize('create', Exam::class);
        return view('exams.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'exam_date' => ['nullable', 'date'],
            'term' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['nullable', 'string', 'max:255'],
        ]);

        $data['teacher_id'] = Auth::user()->teacher?->id;

        $exam = Exam::create($data);

        ActivityLogger::log('exam-created', 'Exam', $exam->id, "Created exam: {$exam->name}");

        return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);
        return view('exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'exam_date' => ['nullable', 'date'],
            'term' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['nullable', 'string', 'max:255'],
        ]);

        $exam->update($data);

        ActivityLogger::log('exam-updated', 'Exam', $exam->id, "Updated exam: {$exam->name}");

        return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);

        ActivityLogger::log('exam-deleted', 'Exam', $exam->id, "Deleted exam: {$exam->name}");
        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }
}
