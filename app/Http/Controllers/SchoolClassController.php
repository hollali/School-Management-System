<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', SchoolClass::class);

        $user = auth()->user();

        $classes = SchoolClass::with('teacher', 'students')
            ->when($user->hasRole('Teacher'), fn ($q) => $q->where('teacher_id', $user->teacher?->id))
            ->latest()->paginate(15);

        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('classes.index', compact('classes', 'teachers'));
    }

    public function show(SchoolClass $class)
    {
        $this->authorize('view', $class);

        $class->load('teacher.user', 'students.user', 'subjects');

        $assignedStudentIds = $class->students->pluck('id');
        $availableStudents = Student::with('user')
            ->whereNotIn('id', $assignedStudentIds)
            ->get();

        return view('classes.show', compact('class', 'availableStudents'));
    }

    public function assignStudent(Request $request, SchoolClass $class)
    {
        $this->authorize('update', $class);

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        if ($class->students()->where('student_id', $data['student_id'])->exists()) {
            return back()->with('error', 'Student is already assigned to this class.');
        }

        if ($class->capacity && $class->students()->count() >= $class->capacity) {
            return back()->with('error', 'Class has reached its capacity limit.');
        }

        $class->students()->attach($data['student_id'], [
            'assigned_at' => now(),
            'status' => 'active',
        ]);

        return back()->with('success', 'Student assigned to class successfully.');
    }

    public function removeStudent(SchoolClass $class, Student $student)
    {
        $this->authorize('update', $class);

        $class->students()->detach($student->id);

        return back()->with('success', 'Student removed from class successfully.');
    }

    public function assignmentPage()
    {
        $classes = SchoolClass::with('students')->latest()->get();
        $students = Student::with('user', 'classes')->get();

        return view('admin.class-assignments', compact('classes', 'students'));
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        $class = SchoolClass::findOrFail($data['class_id']);

        $existingIds = $class->students()->pluck('student_id')->toArray();
        $newIds = array_diff($data['student_ids'], $existingIds);

        if (empty($newIds)) {
            return back()->with('error', 'All selected students are already assigned to this class.');
        }

        if ($class->capacity) {
            $slotsAvailable = $class->capacity - $class->students()->count();
            if (count($newIds) > $slotsAvailable) {
                return back()->with('error', "Only {$slotsAvailable} slot(s) available in this class. Selected " . count($newIds) . " student(s).");
            }
        }

        $now = now();
        $attachData = [];
        foreach ($newIds as $studentId) {
            $attachData[$studentId] = [
                'assigned_at' => $now,
                'status' => 'active',
            ];
        }

        $class->students()->attach($attachData);

        $count = count($newIds);
        return redirect()->route('admin.class-assignments')->with('success', "{$count} student(s) assigned to {$class->name} successfully.");
    }

    public function create()
    {
        return redirect()->route('classes.index');
    }

    public function store(Request $request)
    {
        $this->authorize('create', SchoolClass::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'grade_level' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:100'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'academic_year' => ['nullable', 'string', 'max:255'],
        ]);

        SchoolClass::create($data);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    public function edit(SchoolClass $class)
    {
        return redirect()->route('classes.index');
    }

    public function update(Request $request, SchoolClass $class)
    {
        $this->authorize('update', $class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'grade_level' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:100'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'academic_year' => ['nullable', 'string', 'max:255'],
        ]);

        $class->update($data);

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $this->authorize('delete', $class);

        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }
}
