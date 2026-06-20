<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceReportController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $classes = collect();
        $subjects = collect();
        $teachers = collect();

        if ($user->hasRole('Admin')) {
            $classes = SchoolClass::orderBy('name')->get();
            $subjects = Subject::orderBy('name')->get();
            $teachers = Teacher::with('user')->get();
        } elseif ($user->hasRole('Teacher')) {
            $classes = SchoolClass::where('teacher_id', $user->teacher?->id)->orderBy('name')->get();
        }

        $reportData = null;
        $reportType = $request->input('report_type', 'class');

        if ($request->has('generate')) {
            $reportData = match ($reportType) {
                'student' => $this->generateStudentReport($request),
                'class' => $this->generateClassReport($request),
                'subject' => $this->generateSubjectReport($request),
                'teacher' => $this->generateTeacherReport($request),
                'date_range' => $this->generateDateRangeReport($request),
                default => null,
            };
        }

        return view('attendance.reports', compact(
            'classes', 'subjects', 'teachers',
            'reportData', 'reportType'
        ));
    }

    private function generateStudentReport(Request $request): array
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $student = Student::with('user', 'classes')->findOrFail($data['student_id']);
        $summary = $this->attendanceService->getStudentAttendanceSummary(
            $student, $data['date_from'] ?? null, $data['date_to'] ?? null
        );

        $records = AttendanceRecord::where('student_id', $student->id)
            ->whereHas('attendance', function ($q) use ($data) {
                if ($data['date_from'] ?? null) $q->where('attendance_date', '>=', $data['date_from']);
                if ($data['date_to'] ?? null) $q->where('attendance_date', '<=', $data['date_to']);
            })
            ->with(['attendance.schoolClass', 'attendance.subject'])
            ->latest('id')
            ->get();

        return [
            'type' => 'student',
            'student' => $student,
            'summary' => $summary,
            'records' => $records,
        ];
    }

    private function generateClassReport(Request $request): array
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $class = SchoolClass::findOrFail($data['class_id']);
        $summaries = $this->attendanceService->getClassAttendanceSummary(
            $class->id, $data['date_from'] ?? null, $data['date_to'] ?? null
        );

        $avgPercentage = $summaries->avg('percentage');

        $attendanceSessions = Attendance::where('class_id', $class->id)
            ->when($data['date_from'] ?? null, fn($q) => $q->where('attendance_date', '>=', $data['date_from']))
            ->when($data['date_to'] ?? null, fn($q) => $q->where('attendance_date', '<=', $data['date_to']))
            ->with('subject')
            ->latest('attendance_date')
            ->get();

        return [
            'type' => 'class',
            'class' => $class,
            'summaries' => $summaries,
            'avg_percentage' => round($avgPercentage, 1),
            'attendance_sessions' => $attendanceSessions,
        ];
    }

    private function generateSubjectReport(Request $request): array
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $subject = Subject::findOrFail($data['subject_id']);
        $attendanceIds = Attendance::where('subject_id', $subject->id)
            ->when($data['date_from'] ?? null, fn($q) => $q->where('attendance_date', '>=', $data['date_from']))
            ->when($data['date_to'] ?? null, fn($q) => $q->where('attendance_date', '<=', $data['date_to']))
            ->pluck('id');

        $records = AttendanceRecord::whereIn('attendance_id', $attendanceIds)
            ->with(['student.user', 'attendance.schoolClass'])
            ->get();

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();

        return [
            'type' => 'subject',
            'subject' => $subject,
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];
    }

    private function generateTeacherReport(Request $request): array
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $teacher = Teacher::with('user')->findOrFail($data['teacher_id']);
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        $attendanceSessions = Attendance::whereIn('class_id', $classIds)
            ->when($data['date_from'] ?? null, fn($q) => $q->where('attendance_date', '>=', $data['date_from']))
            ->when($data['date_to'] ?? null, fn($q) => $q->where('attendance_date', '<=', $data['date_to']))
            ->with('schoolClass')
            ->latest('attendance_date')
            ->get();

        $sessionCount = $attendanceSessions->count();
        $totalRecords = AttendanceRecord::whereIn('attendance_id', $attendanceSessions->pluck('id'))->count();

        return [
            'type' => 'teacher',
            'teacher' => $teacher,
            'session_count' => $sessionCount,
            'total_records' => $totalRecords,
            'attendance_sessions' => $attendanceSessions,
        ];
    }

    private function generateDateRangeReport(Request $request): array
    {
        $data = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        ]);

        $summary = $this->attendanceService->getOverallSchoolSummary($data['date_from'], $data['date_to']);

        $classSummaries = SchoolClass::withCount('students')->get()->map(function ($class) use ($data) {
            $classSummary = $this->attendanceService->getClassAttendanceSummary($class->id, $data['date_from'], $data['date_to']);
            $avg = $classSummary->avg('percentage');
            return [
                'class' => $class,
                'avg_percentage' => round($avg, 1),
                'student_count' => $class->students_count,
            ];
        });

        $dailyBreakdown = Attendance::whereBetween('attendance_date', [$data['date_from'], $data['date_to']])
            ->with('schoolClass')
            ->latest('attendance_date')
            ->get()
            ->groupBy('attendance_date')
            ->map(function ($sessions, $date) {
                $records = AttendanceRecord::whereIn('attendance_id', $sessions->pluck('id'))->get();
                return [
                    'date' => $date,
                    'total' => $records->count(),
                    'present' => $records->where('status', 'present')->count(),
                    'absent' => $records->where('status', 'absent')->count(),
                    'late' => $records->where('status', 'late')->count(),
                    'excused' => $records->where('status', 'excused')->count(),
                ];
            })->sortBy('date');

        return [
            'type' => 'date_range',
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
            'summary' => $summary,
            'class_summaries' => $classSummaries,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }
}
