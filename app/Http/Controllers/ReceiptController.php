<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Receipt::with(['payment.fee.student.user', 'generator']);

        if ($user->hasRole('Student')) {
            $studentId = $user->student?->id;
            $query->whereHas('payment', fn($q) => $q->where('student_id', $studentId));
        } elseif ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $query->whereHas('payment', fn($q) => $q->whereIn('student_id', $studentIds));
        } elseif ($user->hasRole('Teacher')) {
            $studentIds = Student::whereHas('classes', fn($q) => $q->whereIn('class_id', $user->teacher?->classes->pluck('id') ?? []))
                ->pluck('id');
            $query->whereHas('payment', fn($q) => $q->whereIn('student_id', $studentIds));
        }

        $receipts = $query->latest()->paginate(15);
        $payments = Payment::with('fee.student.user')->get();

        return view('receipts.index', compact('receipts', 'payments'));
    }

    public function show(Receipt $receipt)
    {
        $user = Auth::user();

        $receipt->load(['payment.fee.student.user', 'payment.student.user', 'payment.parentProfile.user', 'generator']);

        if ($user->hasRole('Student') && $receipt->payment->student_id !== $user->student?->id) {
            abort(403);
        }
        if ($user->hasRole('Parent') && !$user->parentProfile?->students->pluck('id')->contains($receipt->payment->student_id)) {
            abort(403);
        }

        return view('receipts.show', compact('receipt'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = Payment::with('fee')->findOrFail($data['payment_id']);

        $receipt = Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => (new \App\Services\FeeService)->generateReceiptNumber(),
            'amount' => $payment->amount,
            'payment_method' => $payment->method,
            'transaction_reference' => $payment->transaction_id ?? $payment->reference,
            'issued_at' => now(),
            'notes' => $data['notes'] ?? null,
            'generated_by' => Auth::id(),
        ]);

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt ' . $receipt->receipt_number . ' generated successfully.');
    }

    public function destroy(Receipt $receipt)
    {
        $this->authorize('manage-users');
        $receipt->delete();

        return redirect()->route('receipts.index')->with('success', 'Receipt deleted successfully.');
    }

    public function exportCsv()
    {
        $receipts = Receipt::with('payment.fee.student.user')->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="receipts.csv"',
        ];

        $callback = function () use ($receipts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Receipt #', 'Student', 'Amount', 'Method', 'Date', 'Reference', 'Notes']);

            foreach ($receipts as $receipt) {
                fputcsv($handle, [
                    $receipt->receipt_number ?? '',
                    $receipt->payment->fee->student->user->name ?? '',
                    number_format($receipt->amount, 2),
                    $receipt->payment_method ?? '',
                    $receipt->issued_at ? $receipt->issued_at->format('Y-m-d H:i') : '',
                    $receipt->transaction_reference ?? '',
                    $receipt->notes ?? '',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
