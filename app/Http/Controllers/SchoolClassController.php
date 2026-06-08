<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $classes = SchoolClass::with('teacher', 'students')->latest()->paginate(15);

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
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
        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('classes.edit', compact('class', 'teachers'));
    }

    public function update(Request $request, SchoolClass $class)
    {
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
        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }
}
