<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('fee.student.user')->latest()->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $fees = Fee::with('student.user')
            ->whereIn('status', ['pending', 'partial'])
            ->get();

        return view('payments.create', compact('fees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fee_id' => ['required', 'exists:fees,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'method' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $payment = Payment::create($data);

        Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'RCP-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            'issued_at' => now(),
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('fee.student.user', 'receipt');

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $fees = Fee::with('student.user')
            ->whereIn('status', ['pending', 'partial'])
            ->orWhere('id', $payment->fee_id)
            ->get();

        return view('payments.edit', compact('payment', 'fees'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'fee_id' => ['required', 'exists:fees,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'method' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $payment->update($data);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->receipt) {
            $payment->receipt->delete();
        }

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
