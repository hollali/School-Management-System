<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use App\Services\FeeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PaymentController extends Controller
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
        $query = Payment::with(['fee.student.user', 'student.user', 'parentProfile.user', 'user', 'receipt']);

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

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        if ($request->filled('date_from')) {
            $query->where('paid_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('paid_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15);
        $fees = Fee::with('student.user')->get();

        return view('payments.index', compact('payments', 'fees'));
    }

    public function show(Payment $payment)
    {
        $user = Auth::user();

        if ($user->hasRole('Student') && $payment->student_id !== $user->student?->id) {
            abort(403);
        }
        if ($user->hasRole('Parent') && !$user->parentProfile?->students->pluck('id')->contains($payment->student_id)) {
            abort(403);
        }

        $payment->load(['fee.student.user', 'student.user', 'parentProfile.user', 'user', 'receipt']);
        return view('payments.show', compact('payment'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'fee_id' => ['required', 'exists:fees,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $fee = Fee::findOrFail($data['fee_id']);

        $payment = $this->feeService->processPayment($fee, $data['amount'], $data['method'] ?? 'manual', [
            'reference' => $data['reference'] ?? null,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'payment_type' => 'manual',
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully. Receipt #' . ($payment->receipt?->receipt_number ?? '') . ' generated.');
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update($data);

        if ($payment->fee) {
            $this->feeService->updateInvoiceBalance($payment->fee);
        }

        ActivityLogger::log('payment-updated', 'Payment', $payment->id, 'Payment record updated');

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('manage-users');

        if ($payment->receipt) {
            $payment->receipt->delete();
        }
        $fee = $payment->fee;
        $payment->delete();

        if ($fee) {
            $this->feeService->updateInvoiceBalance($fee);
        }

        ActivityLogger::log('payment-deleted', 'Payment', $payment->id, 'Payment deleted');

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    public function parentPay(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Parent')) {
            abort(403);
        }

        $parent = $user->parentProfile;
        if (!$parent) {
            abort(404, 'Parent profile not found.');
        }

        $studentIds = $parent->students->pluck('id');

        $data = $request->validate([
            'fee_id' => ['required', 'exists:fees,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $fee = Fee::with('student')->findOrFail($data['fee_id']);

        if (!$studentIds->contains($fee->student_id)) {
            return back()->withErrors(['fee_id' => 'You can only pay for your own children.']);
        }

        $payment = $this->feeService->processPayment($fee, $data['amount'], $data['method'], [
            'parent_id' => $parent->id,
            'reference' => $data['reference'] ?? null,
            'payment_type' => 'parent',
            'status' => 'completed',
        ]);

        return redirect()->route('payments.parent.history')
            ->with('success', 'Payment of $' . number_format($data['amount'], 2) . ' completed successfully.');
    }

    public function parentHistory()
    {
        $user = Auth::user();
        if (!$user->hasRole('Parent')) {
            abort(403);
        }

        $parent = $user->parentProfile;
        if (!$parent) {
            abort(404);
        }

        $studentIds = $parent->students->pluck('id');

        $payments = Payment::where('parent_id', $parent->id)
            ->with(['fee', 'student.user', 'receipt'])
            ->latest()
            ->paginate(15);

        $outstandingInvoices = Fee::whereIn('student_id', $studentIds)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->with('student.user')
            ->get();

        return view('payments.parent-history', compact('payments', 'outstandingInvoices'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $query = Payment::with('fee.student.user');

        if ($user->hasRole('Admin')) {
        } elseif ($user->hasRole('Student')) {
            $query->where('student_id', $user->student?->id);
        } elseif ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $query->whereIn('student_id', $studentIds);
        } else {
            $query->whereRaw('1 = 0');
        }

        $payments = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="payments.csv"',
        ];

        $callback = function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Receipt', 'Student', 'Amount', 'Method', 'Date', 'Type', 'Reference', 'Status']);

            foreach ($payments as $payment) {
                fputcsv($handle, [
                    $payment->receipt?->receipt_number ?? '',
                    $payment->student?->user?->name ?? $payment->fee?->student?->user?->name ?? '',
                    number_format($payment->amount, 2),
                    $payment->method ?? '',
                    $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '',
                    $payment->payment_type ?? '',
                    $payment->reference ?? '',
                    $payment->status ?? '',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
