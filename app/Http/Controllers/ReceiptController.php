<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReceiptController extends Controller
{
    public function index()
    {
        $receipts = Receipt::with('payment.fee.student.user')->latest()->paginate(15);

        $payments = Payment::with('fee.student.user')->get();

        return view('receipts.index', compact('receipts', 'payments'));
    }

    public function create()
    {
        return redirect()->route('receipts.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Receipt::create($data);

        return redirect()->route('receipts.index')->with('success', 'Receipt created successfully.');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('payment.fee.student.user');

        return view('receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        return redirect()->route('receipts.index');
    }

    public function update(Request $request, Receipt $receipt)
    {
        $data = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $receipt->update($data);

        return redirect()->route('receipts.index')->with('success', 'Receipt updated successfully.');
    }

    public function destroy(Receipt $receipt)
    {
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
            fputcsv($handle, ['Receipt #', 'Payment Ref', 'Student', 'Amount', 'Issued Date', 'Notes']);

            foreach ($receipts as $receipt) {
                fputcsv($handle, [
                    $receipt->receipt_number ?? '',
                    $receipt->payment->reference ?? '',
                    $receipt->payment->fee->student->user->name ?? '',
                    number_format($receipt->payment->amount, 2),
                    $receipt->issued_at ? $receipt->issued_at->format('Y-m-d') : '',
                    $receipt->notes ?? '',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
