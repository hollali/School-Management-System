<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        $fees = Fee::with('student.user')->latest()->paginate(15);

        return view('fees.index', compact('fees'));
    }

    public function create()
    {
        $students = Student::with('user')->orderBy('id')->get();

        return view('fees.create', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        Fee::create($data);

        return redirect()->route('fees.index')->with('success', 'Fee record saved successfully.');
    }

    public function show(Fee $fee)
    {
        $fee->load('student.user', 'payments');

        return view('fees.show', compact('fee'));
    }

    public function edit(Fee $fee)
    {
        $students = Student::with('user')->orderBy('id')->get();

        return view('fees.edit', compact('fee', 'students'));
    }

    public function update(Request $request, Fee $fee)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $fee->update($data);

        return redirect()->route('fees.index')->with('success', 'Fee record updated successfully.');
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();

        return redirect()->route('fees.index')->with('success', 'Fee record deleted successfully.');
    }
}
