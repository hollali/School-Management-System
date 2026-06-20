<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Services\AttendanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
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
        $query = Attendance::with(['schoolClass', 'creator', 'teacher.user', 'subject']);

        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->teacher?->id);
        } elseif ($user->hasRole('Student')) {
            $classIds = $user->student?->classes->pluck('id') ?? [];
            $query->whereIn('class_id', $classIds);
        } elseif ($user->hasRole('Parent')) {
            $classIds = $user->parentProfile?->students->flatMap->classes->pluck('id') ?? [];
            $query->whereIn('class_id', $classIds);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        $attendances = $query->latest('attendance_date')->latest('created_at')->paginate(15);

        $classes = collect();
        $subjects = collect();
        if ($user->hasRole('Teacher')) {
            $teacher = $user->teacher;
            $classes = SchoolClass::where('teacher_id', $teacher->id)->orderBy('name')->get();
        } elseif ($user->hasRole('Admin')) {
            $classes = SchoolClass::orderBy('name')->get();
            $subjects = Subject::orderBy('name')->get();
        }

        return view('attendance.index', compact('attendances', 'classes', 'subjects'));
    }

    public function mark(Request $request)
    {
        $this->authorize('mark', Attendance::class);

        $user = Auth::user();
        $teacherId = $user->teacher?->id;

        $classes = SchoolClass::when(!$user->hasRole('Admin'), function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->orderBy('name')->get();

        $classId = $request->input('class_id');
        $date = $request->input('attendance_date', now()->format('Y-m-d'));
        $subjectId = $request->input('subject_id');

        $students = collect();
        $attendance = null;
        $isExisting = false;

        if ($classId && $date) {
            $attendance = $this->attendanceService->getOrCreateAttendance($classId, $date, $subjectId, $teacherId);
            $isExisting = $attendance->wasRecentlyCreated === false && $attendance->exists;

            $attendance->load(['records.student.user', 'schoolClass', 'subject']);

            $students = $attendance->records->map(function ($record) {
                return $record;
            });
        }

        $subjects = $user->hasRole('Admin')
            ? Subject::orderBy('name')->get()
            : collect();

        return view('attendance.mark', compact(
            'classes', 'subjects', 'students', 'attendance',
            'classId', 'date', 'subjectId', 'isExisting'
        ));
    }

    public function storeMark(Request $request)
    {
        $this->authorize('mark', Attendance::class);

        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'attendance_date' => ['required', 'date'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'students' => ['required', 'array'],
            'students.*.status' => ['required', 'in:present,absent,late,excused'],
            'students.*.remarks' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = Auth::user();
        $teacherId = $user->teacher?->id;

        if (!$user->hasRole('Admin')) {
            $class = SchoolClass::findOrFail($data['class_id']);
            if ($class->teacher_id !== $teacherId) {
                return back()->withErrors(['class_id' => 'You can only mark attendance for your own classes.']);
            }
        }

        $attendance = $this->attendanceService->getOrCreateAttendance(
            $data['class_id'], $data['attendance_date'],
            $data['subject_id'], $teacherId
        );

        $attendance->update(['notes' => $data['notes'] ?? $attendance->notes]);

        $this->attendanceService->markBulkAttendance($attendance->id, $data['students']);

        $className = $attendance->schoolClass?->name ?? 'Class';
        ActivityLogger::log('attendance-marked', 'Attendance', $attendance->id,
            "Marked attendance for {$className} on {$data['attendance_date']}");

        return redirect()->route('attendance.mark', [
            'class_id' => $data['class_id'],
            'attendance_date' => $data['attendance_date'],
            'subject_id' => $data['subject_id'],
        ])->with('success', 'Attendance recorded successfully.');
    }

    public function show(Attendance $attendance)
    {
        $user = Auth::user();

        $this->authorize('view', $attendance);

        if ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $attendance->load(['schoolClass', 'creator', 'subject', 'teacher.user',
                'records' => fn($q) => $q->whereIn('student_id', $studentIds),
                'records.student.user']);
        } else {
            $attendance->load(['schoolClass', 'creator', 'subject', 'teacher.user', 'records.student.user']);
        }

        return view('attendance.show', compact('attendance'));
    }

    public function updateRecord(Request $request, AttendanceRecord $record)
    {
        $this->authorize('update', $record->attendance);

        $data = $request->validate([
            'status' => ['required', 'in:present,absent,late,excused'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $this->attendanceService->markStudentAttendance($record, $data['status'], $data['remarks']);

        return back()->with('success', 'Attendance record updated.');
    }

    public function destroy(Attendance $attendance)
    {
        $this->authorize('delete', $attendance);

        $className = $attendance->schoolClass?->name ?? 'Class';
        ActivityLogger::log('attendance-deleted', 'Attendance', $attendance->id,
            "Deleted attendance for {$className} on {$attendance->attendance_date}");

        $attendance->delete();

        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }

    public function studentShow()
    {
        $user = Auth::user();
        if (!$user->hasRole('Student')) {
            abort(403);
        }

        $student = $user->student;
        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        $summary = $this->attendanceService->getStudentAttendanceSummary($student);

        $records = AttendanceRecord::where('student_id', $student->id)
            ->with(['attendance.schoolClass', 'attendance.subject'])
            ->latest('id')
            ->paginate(20);

        return view('attendance.student-show', compact('summary', 'records'));
    }
}
