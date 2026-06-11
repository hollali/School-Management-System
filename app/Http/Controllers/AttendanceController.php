<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['schoolClass', 'creator'])->latest()->paginate(15);

        return view('attendance.index', compact('attendances'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('attendance.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'attendance_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance = Attendance::create([
            'class_id' => $data['class_id'],
            'attendance_date' => $data['attendance_date'],
            'created_by' => Auth::id(),
            'notes' => $data['notes'] ?? null,
        ]);

        $schoolClass = SchoolClass::find($data['class_id']);

        foreach ($schoolClass->students as $student) {
            AttendanceRecord::create([
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'status' => 'present',
            ]);
        }

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Attendance $attendance)
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('attendance.edit', compact('attendance', 'classes'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'attendance_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance->update($data);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
