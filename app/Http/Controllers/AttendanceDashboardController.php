<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceDashboardController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Student')) {
            return $this->studentDashboard($user);
        }

        if ($user->hasRole('Teacher')) {
            return $this->teacherDashboard($user, $request);
        }

        if ($user->hasRole('Admin')) {
            return $this->adminDashboard($request);
        }

        abort(403);
    }

    private function studentDashboard($user)
    {
        $student = $user->student;
        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        $summary = $this->attendanceService->getStudentAttendanceSummary($student);
        $recentRecords = AttendanceRecord::where('student_id', $student->id)
            ->with(['attendance.schoolClass', 'attendance.subject'])
            ->latest('id')
            ->take(10)
            ->get();
        $belowThreshold = $summary['percentage'] < AttendanceService::THRESHOLD_DEFAULT;

        return view('attendance.dashboard', compact('summary', 'recentRecords', 'belowThreshold'))
            ->with('role', 'student');
    }

    private function teacherDashboard($user, Request $request)
    {
        $teacher = $user->teacher;
        if (!$teacher) {
            abort(404, 'Teacher profile not found.');
        }

        $classes = SchoolClass::where('teacher_id', $teacher->id)->get();
        $classIds = $classes->pluck('id');

        $today = now()->format('Y-m-d');

        $todayAttendance = Attendance::whereIn('class_id', $classIds)
            ->where('attendance_date', $today)
            ->with('schoolClass')
            ->get();

        $missingClasses = $classes->filter(function ($class) use ($todayAttendance) {
            return !$todayAttendance->contains('class_id', $class->id);
        });

        $lowAttendanceStudents = $this->attendanceService->getLowAttendanceStudents();

        $classSummaries = $classes->map(function ($class) {
            return [
                'class' => $class,
                'summary' => $this->attendanceService->getClassAttendanceSummary($class->id),
            ];
        });

        $recentAttendance = Attendance::whereIn('class_id', $classIds)
            ->with('schoolClass')
            ->latest('attendance_date')
            ->take(5)
            ->get();

        return view('attendance.dashboard', compact(
            'classes', 'todayAttendance', 'missingClasses',
            'lowAttendanceStudents', 'classSummaries', 'recentAttendance'
        ))->with('role', 'teacher');
    }

    private function adminDashboard(Request $request)
    {
        $startDate = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('date_to', now()->format('Y-m-d'));

        $overallSummary = $this->attendanceService->getOverallSchoolSummary($startDate, $endDate);

        $classes = SchoolClass::withCount('students')->get();

        $classPerformance = $classes->map(function ($class) use ($startDate, $endDate) {
            $summary = $this->attendanceService->getClassAttendanceSummary($class->id, $startDate, $endDate);
            $avgPercentage = $summary->avg('percentage');
            return [
                'class' => $class,
                'avg_percentage' => round($avgPercentage, 1),
                'total_students' => $class->students_count,
            ];
        })->sortBy('avg_percentage');

        $lowAttendanceStudents = $this->attendanceService->getLowAttendanceStudents();

        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('attendance_date', $today)
            ->with('schoolClass')
            ->get();

        $recentTrend = Attendance::whereBetween('attendance_date', [now()->subDays(30)->format('Y-m-d'), $today])
            ->selectRaw('attendance_date, count(*) as total')
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get();

        return view('attendance.dashboard', compact(
            'overallSummary', 'classes', 'classPerformance',
            'lowAttendanceStudents', 'todayAttendance',
            'startDate', 'endDate', 'recentTrend'
        ))->with('role', 'admin');
    }
}
