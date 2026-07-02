<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Subject::class);

        $subjects = Subject::with('teacher')->latest()->paginate(15);
        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('subjects.index', compact('subjects', 'teachers'));
    }

    public function create()
    {
        return redirect()->route('subjects.index');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Subject::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'credits' => ['nullable', 'integer', 'min:0'],
        ]);

        Subject::create($data);

        return redirect()->route('subjects.index')->with('success', 'Subject saved successfully.');
    }

    public function edit(Subject $subject)
    {
        return redirect()->route('subjects.index');
    }

    public function update(Request $request, Subject $subject)
    {
        $this->authorize('update', $subject);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'credits' => ['nullable', 'integer', 'min:0'],
        ]);

        $subject->update($data);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $this->authorize('delete', $subject);

        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
