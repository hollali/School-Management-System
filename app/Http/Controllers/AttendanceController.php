<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $user = Auth::user();
        $query = Attendance::with(['schoolClass', 'creator']);

        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->teacher?->id);
        } elseif ($user->hasRole('Student')) {
            $classIds = $user->student?->classes->pluck('id') ?? [];
            $query->whereIn('class_id', $classIds);
        } elseif ($user->hasRole('Parent')) {
            $classIds = $user->parentProfile?->students->flatMap->classes->pluck('id') ?? [];
            $query->whereIn('class_id', $classIds);
        }

        $attendances = $query->latest()->paginate(15);

        $classes = collect();
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            $classes = SchoolClass::where('teacher_id', $teacher->id)->orderBy('name')->get();
        }

        return view('attendance.index', compact('attendances', 'classes'));
    }

    public function show(Attendance $attendance)
    {
        $user = Auth::user();

        if ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $attendance->load(['schoolClass', 'creator', 'records' => fn($q) => $q->whereIn('student_id', $studentIds), 'records.student.user']);
        } else {
            $attendance->load(['schoolClass', 'creator', 'records.student.user']);
        }

        return view('attendance.show', compact('attendance'));
    }

    public function create()
    {
        $this->authorize('create', Attendance::class);

        return redirect()->route('attendance.index');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Attendance::class);

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
            'teacher_id' => Auth::user()->teacher?->id,
        ]);

        $schoolClass = SchoolClass::find($data['class_id']);

        foreach ($schoolClass->students as $student) {
            AttendanceRecord::create([
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'status' => 'present',
            ]);
        }

        ActivityLogger::log('attendance-recorded', 'Attendance', $attendance->id, "Recorded attendance for {$schoolClass->name} on {$data['attendance_date']}");

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Attendance $attendance)
    {
        $this->authorize('update', $attendance);

        return redirect()->route('attendance.index');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $this->authorize('update', $attendance);

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
        $this->authorize('delete', $attendance);

        ActivityLogger::log('attendance-deleted', 'Attendance', $attendance->id, 'Deleted attendance record');
        $attendance->delete();

        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
