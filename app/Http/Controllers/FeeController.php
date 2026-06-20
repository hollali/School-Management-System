<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Discount;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\FeeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class FeeController extends Controller
{
    use AuthorizesRequests;

    protected FeeService $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Fee::with(['student.user', 'feeStructure', 'creator']);

        if ($user->hasRole('Admin')) {
        } elseif ($user->hasRole('Teacher')) {
            $studentIds = Student::whereHas('classes', fn($q) => $q->whereIn('class_id', $user->teacher?->classes->pluck('id') ?? []))
                ->pluck('id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->hasRole('Student')) {
            $query->where('student_id', $user->student?->id);
        } elseif ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $query->whereIn('student_id', $studentIds);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('academic_term')) {
            $query->where('academic_term', $request->academic_term);
        }
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $fees = $query->latest()->paginate(15);
        $students = Student::with('user')->orderBy('id')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $terms = Fee::distinct()->pluck('academic_term')->filter();
        $years = Fee::distinct()->pluck('academic_year')->filter();

        return view('fees.index', compact('fees', 'students', 'classes', 'terms', 'years'));
    }

    public function show(Fee $fee)
    {
        $user = Auth::user();

        if ($user->hasRole('Student') && $fee->student_id !== $user->student?->id) {
            abort(403);
        }
        if ($user->hasRole('Parent') && !$user->parentProfile?->students->pluck('id')->contains($fee->student_id)) {
            abort(403);
        }

        $fee->load(['student.user', 'student.parent.user', 'items.category', 'payments.receipt', 'feeStructure', 'creator']);

        return view('fees.show', compact('fee'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string|max:1000'],
        ]);

        $fee = Fee::create([
            'student_id' => $data['student_id'],
            'invoice_number' => $this->feeService->generateInvoiceNumber(),
            'amount' => $data['amount'],
            'paid_amount' => 0,
            'balance' => $data['amount'],
            'due_date' => $data['due_date'] ?? null,
            'issue_date' => now(),
            'status' => 'active',
            'payment_status' => 'unpaid',
            'description' => $data['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        ActivityLogger::log('fee-created', 'Fee', $fee->id,
            "Created invoice {$fee->invoice_number} for student #{$data['student_id']}: \${$data['amount']}");

        return redirect()->route('fees.index')->with('success', 'Invoice created successfully.');
    }

    public function update(Request $request, Fee $fee)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string|max:1000'],
        ]);

        $oldAmount = $fee->amount;
        $balanceChange = $data['amount'] - $oldAmount;
        $newBalance = $fee->balance + $balanceChange;

        $fee->update([
            'amount' => $data['amount'],
            'balance' => max(0, $newBalance),
            'due_date' => $data['due_date'] ?? $fee->due_date,
            'description' => $data['description'] ?? $fee->description,
            'updated_by' => Auth::id(),
        ]);

        $this->feeService->updateInvoiceBalance($fee->fresh());

        ActivityLogger::log('fee-updated', 'Fee', $fee->id, "Updated invoice {$fee->invoice_number}");

        return redirect()->route('fees.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Fee $fee)
    {
        $this->authorize('manage-users');

        ActivityLogger::log('fee-deleted', 'Fee', $fee->id, "Deleted invoice {$fee->invoice_number}");
        $fee->delete();

        return redirect()->route('fees.index')->with('success', 'Invoice deleted successfully.');
    }

    public function generateInvoices(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'fee_structure_id' => ['required', 'exists:fee_structures,id'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        $invoices = $this->feeService->bulkGenerateInvoices(
            $data['fee_structure_id'],
            $data['student_ids'] ?? null
        );

        if ($invoices->isEmpty()) {
            return redirect()->route('fees.index')
                ->with('info', 'No new invoices were generated. Students may already have invoices for this term.');
        }

        return redirect()->route('fees.index')
            ->with('success', $invoices->count() . ' invoice(s) generated successfully.');
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $query = Fee::with('student.user');

        if ($user->hasRole('Admin')) {
        } elseif ($user->hasRole('Teacher')) {
            $studentIds = Student::whereHas('classes', fn($q) => $q->whereIn('class_id', $user->teacher?->classes->pluck('id') ?? []))->pluck('id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->hasRole('Student')) {
            $query->where('student_id', $user->student?->id);
        } elseif ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $query->whereIn('student_id', $studentIds);
        }

        $fees = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="invoices.csv"',
        ];

        $callback = function () use ($fees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice #', 'Student', 'Amount', 'Paid', 'Balance', 'Due Date', 'Status', 'Term', 'Year']);

            foreach ($fees as $fee) {
                fputcsv($handle, [
                    $fee->invoice_number ?? '',
                    $fee->student->user->name ?? '',
                    number_format($fee->amount, 2),
                    number_format($fee->paid_amount, 2),
                    number_format($fee->balance, 2),
                    $fee->due_date ? $fee->due_date->format('Y-m-d') : '',
                    $fee->payment_status ?? '',
                    $fee->academic_term ?? '',
                    $fee->academic_year ?? '',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
