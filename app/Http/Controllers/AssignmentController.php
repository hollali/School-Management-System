<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['teacher.user', 'schoolClass', 'subject'])
            ->latest()
            ->paginate(15);

        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $classes = SchoolClass::latest()->get();
        $subjects = Subject::latest()->get();
        $teachers = Teacher::with('user')->latest()->get();

        return view('assignments.create', compact('classes', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_id'   => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title'      => ['required', 'string', 'max:255'],
            'description'=> ['nullable', 'string'],
            'due_date'   => ['nullable', 'date'],
        ]);

        Assignment::create($data);

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['submissions.student.user', 'teacher.user', 'schoolClass', 'subject']);

        return view('assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $classes = SchoolClass::latest()->get();
        $subjects = Subject::latest()->get();
        $teachers = Teacher::with('user')->latest()->get();

        return view('assignments.edit', compact('assignment', 'classes', 'subjects', 'teachers'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_id'   => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title'      => ['required', 'string', 'max:255'],
            'description'=> ['nullable', 'string'],
            'due_date'   => ['nullable', 'date'],
        ]);

        $assignment->update($data);

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }
}
