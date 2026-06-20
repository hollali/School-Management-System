<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceDashboardController extends Controller
{
    protected FeeService $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return $this->adminDashboard($request);
        }

        if ($user->hasRole('Student')) {
            return $this->studentDashboard($user);
        }

        if ($user->hasRole('Parent')) {
            return $this->parentDashboard($user);
        }

        abort(403);
    }

    private function adminDashboard(Request $request)
    {
        $startDate = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('date_to', now()->format('Y-m-d'));

        $revenue = $this->feeService->getOverallRevenueSummary($startDate, $endDate);
        $outstanding = $this->feeService->getOutstandingSummary();

        $classSummaries = SchoolClass::withCount('students')->get()->map(function ($class) {
            $summary = $this->feeService->getClassFinancialSummary($class->id);
            return array_merge(['class' => $class], $summary);
        });

        $recentPayments = Payment::with(['fee.student.user', 'parentProfile.user'])
            ->latest()
            ->take(10)
            ->get();

        $overdueInvoices = Fee::with('student.user')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->where('due_date', '<', now())
            ->take(10)
            ->get();

        $monthlyRevenue = Payment::where('status', 'completed')
            ->where('paid_at', '>=', now()->subMonths(12))
            ->selectRaw("strftime('%Y-%m', paid_at) as month, sum(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('finance.dashboard', compact(
            'revenue', 'outstanding', 'classSummaries',
            'recentPayments', 'overdueInvoices', 'monthlyRevenue',
            'startDate', 'endDate'
        ))->with('role', 'admin');
    }

    private function studentDashboard($user)
    {
        $student = $user->student;
        if (!$student) abort(404);

        $summary = $this->feeService->getStudentFeeSummary($student);
        $invoices = Fee::where('student_id', $student->id)
            ->with('items.category')
            ->latest()
            ->paginate(15);

        return view('finance.dashboard', compact('summary', 'invoices'))
            ->with('role', 'student');
    }

    private function parentDashboard($user)
    {
        $parent = $user->parentProfile;
        if (!$parent) abort(404);

        $studentIds = $parent->students->pluck('id');
        $students = $parent->students()->with('user')->get();

        $childrenSummaries = $students->map(function ($student) {
            return [
                'student' => $student,
                'summary' => $this->feeService->getStudentFeeSummary($student),
            ];
        });

        $outstandingInvoices = Fee::whereIn('student_id', $studentIds)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->with('student.user')
            ->get();

        $recentPayments = Payment::where('parent_id', $parent->id)
            ->with(['fee.student.user', 'receipt'])
            ->latest()
            ->take(10)
            ->get();

        return view('finance.dashboard', compact(
            'students', 'childrenSummaries', 'outstandingInvoices', 'recentPayments'
        ))->with('role', 'parent');
    }
}
