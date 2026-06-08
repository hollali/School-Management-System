<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\Subject;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $homeworks = Homework::with('subject')->latest()->paginate(15);

        return view('homeworks.index', compact('homeworks'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();

        return view('homeworks.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        Homework::create($data);

        return redirect()->route('homeworks.index')->with('success', 'Homework assigned successfully.');
    }

    public function edit(Homework $homework)
    {
        $subjects = Subject::orderBy('name')->get();

        return view('homeworks.edit', compact('homework', 'subjects'));
    }

    public function update(Request $request, Homework $homework)
    {
        $data = $request->validate([
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        $homework->update($data);

        return redirect()->route('homeworks.index')->with('success', 'Homework updated successfully.');
    }

    public function destroy(Homework $homework)
    {
        $homework->delete();

        return redirect()->route('homeworks.index')->with('success', 'Homework deleted successfully.');
    }
}
