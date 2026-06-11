<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index()
    {
        $receipts = Receipt::with('payment.fee.student.user')->latest()->paginate(15);

        return view('receipts.index', compact('receipts'));
    }

    public function create()
    {
        $payments = Payment::with('fee.student.user')->get();

        return view('receipts.create', compact('payments'));
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
        $payments = Payment::with('fee.student.user')->get();

        return view('receipts.edit', compact('receipt', 'payments'));
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
}
