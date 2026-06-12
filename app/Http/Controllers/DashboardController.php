<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Conversation;
use App\Models\Fee;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('Teacher')) {
            return $this->teacherDashboard();
        } elseif ($user->hasRole('Student')) {
            return $this->studentDashboard();
        } elseif ($user->hasRole('Parent')) {
            return $this->parentDashboard();
        }

        return redirect()->route('login');
    }

    protected function adminDashboard()
    {
        $stats = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'classes' => SchoolClass::count(),
            'parents' => ParentProfile::count(),
            'todayAttendance' => Attendance::whereDate('attendance_date', Carbon::today())->count(),
            'pendingFees' => Fee::where('status', 'pending')->count(),
            'pendingAssignments' => Assignment::where('due_date', '>=', Carbon::now())->count(),
            'activeConversations' => Conversation::count(),
        ];

        return view('dashboards.admin', compact('stats'));
    }

    protected function teacherDashboard()
    {
        $teacher = Auth::user()->teacher;

        $stats = [
            'myClasses' => SchoolClass::where('teacher_id', $teacher->id)->count(),
            'myStudents' => Student::whereIn('id', SchoolClass::where('teacher_id', $teacher->id)->with('students')->get()->pluck('students.*.id')->flatten())->count(),
            'myAssignments' => Assignment::where('teacher_id', $teacher->id)->count(),
            'todayAttendance' => Attendance::where('teacher_id', $teacher->id)->whereDate('attendance_date', Carbon::today())->count(),
        ];

        $recentAssignments = Assignment::where('teacher_id', $teacher->id)->latest()->take(5)->get();
        $classes = SchoolClass::where('teacher_id', $teacher->id)->withCount('students')->get();

        return view('dashboards.teacher', compact('stats', 'recentAssignments', 'classes'));
    }

    protected function studentDashboard()
    {
        $student = Auth::user()->student;
        $student->load('classes');

        $stats = [
            'subjects' => $student->classes->flatMap->subjects->count(),
            'assignments' => Assignment::whereIn('class_id', $student->classes->pluck('id'))->count(),
            'attendance' => $student->attendanceRecords()->count() ?? 0,
        ];

        $classes = $student->classes;
        $recentAssignments = Assignment::whereIn('class_id', $classes->pluck('id'))->latest()->take(5)->get();
        $results = $student->results()->with('subject', 'exam')->latest()->take(5)->get();

        return view('dashboards.student', compact('stats', 'classes', 'recentAssignments', 'results'));
    }

    protected function parentDashboard()
    {
        $parent = Auth::user()->parentProfile;
        $children = Student::where('parent_id', $parent->id)->with('user', 'classes')->get();

        return view('dashboards.parent', compact('children', 'parent'));
    }
}
