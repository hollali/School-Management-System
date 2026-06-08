<?php

namespace App\Http\Controllers;

use App\Models\Behavior;
use App\Models\Student;
use Illuminate\Http\Request;

class BehaviorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $behaviors = Behavior::with('student.user')->latest()->paginate(15);

        return view('behaviors.index', compact('behaviors'));
    }

    public function create()
    {
        $students = Student::with('user')->orderBy('id')->get();

        return view('behaviors.create', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        Behavior::create($data);

        return redirect()->route('behaviors.index')->with('success', 'Behavior record added successfully.');
    }

    public function edit(Behavior $behavior)
    {
        $students = Student::with('user')->orderBy('id')->get();

        return view('behaviors.edit', compact('behavior', 'students'));
    }

    public function update(Request $request, Behavior $behavior)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $behavior->update($data);

        return redirect()->route('behaviors.index')->with('success', 'Behavior record updated successfully.');
    }

    public function destroy(Behavior $behavior)
    {
        $behavior->delete();

        return redirect()->route('behaviors.index')->with('success', 'Behavior record deleted successfully.');
    }
}
