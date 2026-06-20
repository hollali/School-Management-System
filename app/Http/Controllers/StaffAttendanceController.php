<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\StaffAttendance;
use App\Models\Teacher;
use App\Services\AttendanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAttendanceController extends Controller
{
    use AuthorizesRequests;

    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = StaffAttendance::with(['teacher.user', 'marker']);

        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->teacher?->id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        $staffAttendances = $query->latest('attendance_date')->latest('created_at')->paginate(15);

        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('staff-attendance.index', compact('staffAttendances', 'teachers'));
    }

    public function create()
    {
        $this->authorize('create', StaffAttendance::class);

        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('staff-attendance.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', StaffAttendance::class);

        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,late,on_leave,excused'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $exists = StaffAttendance::where('teacher_id', $data['teacher_id'])
            ->where('attendance_date', $data['attendance_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['attendance_date' => 'Attendance already recorded for this teacher on this date.'])
                ->withInput();
        }

        $checkIn = $data['check_in'] ? now()->parse($data['check_in']) : null;
        $checkOut = $data['check_out'] ? now()->parse($data['check_out']) : null;

        $staffAttendance = StaffAttendance::create([
            'teacher_id' => $data['teacher_id'],
            'attendance_date' => $data['attendance_date'],
            'status' => $data['status'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'remarks' => $data['remarks'] ?? null,
            'marked_by' => Auth::id(),
        ]);

        $teacherName = $staffAttendance->teacher?->user?->name ?? 'Teacher #' . $data['teacher_id'];
        ActivityLogger::log('staff-attendance-created', 'StaffAttendance', $staffAttendance->id,
            "Recorded {$data['status']} attendance for {$teacherName} on {$data['attendance_date']}");

        return redirect()->route('staff-attendance.index')
            ->with('success', 'Staff attendance recorded successfully.');
    }

    public function show(StaffAttendance $staffAttendance)
    {
        $this->authorize('view', $staffAttendance);

        $staffAttendance->load(['teacher.user', 'marker']);

        return view('staff-attendance.show', compact('staffAttendance'));
    }

    public function edit(StaffAttendance $staffAttendance)
    {
        $this->authorize('update', $staffAttendance);

        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('staff-attendance.edit', compact('staffAttendance', 'teachers'));
    }

    public function update(Request $request, StaffAttendance $staffAttendance)
    {
        $this->authorize('update', $staffAttendance);

        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,late,on_leave,excused'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $checkIn = $data['check_in'] ? now()->parse($data['check_in']) : null;
        $checkOut = $data['check_out'] ? now()->parse($data['check_out']) : null;

        $staffAttendance->update([
            'teacher_id' => $data['teacher_id'],
            'attendance_date' => $data['attendance_date'],
            'status' => $data['status'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'remarks' => $data['remarks'] ?? null,
        ]);

        ActivityLogger::log('staff-attendance-updated', 'StaffAttendance', $staffAttendance->id,
            'Updated staff attendance record');

        return redirect()->route('staff-attendance.index')
            ->with('success', 'Staff attendance updated successfully.');
    }

    public function destroy(StaffAttendance $staffAttendance)
    {
        $this->authorize('delete', $staffAttendance);

        ActivityLogger::log('staff-attendance-deleted', 'StaffAttendance', $staffAttendance->id,
            'Deleted staff attendance record');

        $staffAttendance->delete();

        return redirect()->route('staff-attendance.index')
            ->with('success', 'Staff attendance deleted successfully.');
    }

    public function checkIn()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return back()->withErrors(['You do not have a teacher profile.']);
        }

        $today = now()->format('Y-m-d');
        $existing = StaffAttendance::where('teacher_id', $teacher->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existing) {
            return back()->with('info', 'You have already checked in today.');
        }

        $staffAttendance = StaffAttendance::create([
            'teacher_id' => $teacher->id,
            'attendance_date' => $today,
            'status' => 'present',
            'check_in' => now(),
            'marked_by' => $user->id,
        ]);

        ActivityLogger::log('staff-check-in', 'StaffAttendance', $staffAttendance->id,
            "Teacher {$user->name} checked in");

        return back()->with('success', 'Check-in recorded successfully.');
    }

    public function checkOut()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return back()->withErrors(['You do not have a teacher profile.']);
        }

        $today = now()->format('Y-m-d');
        $existing = StaffAttendance::where('teacher_id', $teacher->id)
            ->where('attendance_date', $today)
            ->first();

        if (!$existing) {
            return back()->withErrors(['You have not checked in today.']);
        }

        if ($existing->check_out) {
            return back()->with('info', 'You have already checked out today.');
        }

        $existing->update(['check_out' => now()]);

        ActivityLogger::log('staff-check-out', 'StaffAttendance', $existing->id,
            "Teacher {$user->name} checked out");

        return back()->with('success', 'Check-out recorded successfully.');
    }
}
