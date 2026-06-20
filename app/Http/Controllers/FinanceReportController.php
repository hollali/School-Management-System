<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceReportController extends Controller
{
    protected FeeService $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index(Request $request)
    {
        $this->authorize('manage-users');

        $classes = SchoolClass::orderBy('name')->get();
        $students = Student::with('user')->orderBy('id')->get();
        $categories = FeeCategory::where('is_active', true)->get();

        $reportData = null;
        $reportType = $request->input('report_type', 'revenue');

        if ($request->has('generate')) {
            $reportData = match ($reportType) {
                'revenue' => $this->revenueReport($request),
                'outstanding' => $this->outstandingReport($request),
                'class' => $this->classReport($request),
                'student' => $this->studentReport($request),
                'category' => $this->categoryReport($request),
                default => null,
            };
        }

        return view('finance.reports', compact(
            'classes', 'students', 'categories', 'reportData', 'reportType'
        ));
    }

    private function revenueReport(Request $request): array
    {
        $startDate = $request->input('date_from', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('date_to', now()->format('Y-m-d'));

        $revenue = $this->feeService->getOverallRevenueSummary($startDate, $endDate);

        $payments = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate . ' 23:59:59'])
            ->with(['fee.student.user', 'parentProfile.user', 'receipt'])
            ->latest('paid_at')
            ->get();

        $dailyTotals = $payments->groupBy(fn($p) => $p->paid_at->format('Y-m-d'))
            ->map(fn($group, $date) => [
                'date' => $date,
                'total' => $group->sum('amount'),
                'count' => $group->count(),
            ])->sortBy('date');

        return [
            'type' => 'revenue',
            'date_from' => $startDate,
            'date_to' => $endDate,
            'revenue' => $revenue,
            'payments' => $payments,
            'daily_totals' => $dailyTotals,
        ];
    }

    private function outstandingReport(Request $request): array
    {
        $outstanding = $this->feeService->getOutstandingSummary();

        $query = Fee::with(['student.user', 'student.classes'])
            ->whereIn('payment_status', ['unpaid', 'partial']);

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->whereHas('classes', fn($cq) => $cq->where('classes.id', $request->class_id)));
        }

        $invoices = $query->orderBy('balance', 'desc')->get();

        return [
            'type' => 'outstanding',
            'outstanding' => $outstanding,
            'invoices' => $invoices,
        ];
    }

    private function classReport(Request $request): array
    {
        $classes = SchoolClass::withCount('students')->get()->map(function ($class) {
            $summary = $this->feeService->getClassFinancialSummary($class->id);
            return array_merge(['class_name' => $class->name], $summary);
        });

        return [
            'type' => 'class',
            'classes' => $classes,
        ];
    }

    private function studentReport(Request $request): array
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $student = Student::with('user', 'classes')->findOrFail($data['student_id']);
        $summary = $this->feeService->getStudentFeeSummary($student);
        $invoices = Fee::where('student_id', $student->id)
            ->with('items.category', 'payments.receipt')
            ->latest()
            ->get();

        return [
            'type' => 'student',
            'student' => $student,
            'summary' => $summary,
            'invoices' => $invoices,
        ];
    }

    private function categoryReport(Request $request): array
    {
        $categories = FeeCategory::with('invoiceItems')->get()->map(function ($category) {
            $items = $category->invoiceItems;
            return [
                'name' => $category->name,
                'total_amount' => $items->sum('amount'),
                'total_discount' => $items->sum('discount_amount'),
                'total_net' => $items->sum('net_amount'),
                'count' => $items->count(),
            ];
        });

        return [
            'type' => 'category',
            'categories' => $categories,
        ];
    }
}
