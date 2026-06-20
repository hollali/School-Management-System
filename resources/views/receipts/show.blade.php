<x-app-layout>
    @section('title', 'Receipt Details')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Receipt Details</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Receipt #{{ $receipt->receipt_number }}</p>
            </div>
            <a href="{{ route('receipts.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-8 max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-200">{{ config('app.name') }}</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400">Payment Receipt</p>
            </div>

            <div class="border-t border-b border-gray-200 dark:border-slate-700 py-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-slate-400">Receipt Number</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $receipt->receipt_number }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-slate-400">Issued Date</span>
                    <span class="text-sm text-gray-900 dark:text-slate-200">{{ $receipt->issued_at ? $receipt->issued_at->format('M d, Y H:i') : '—' }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-slate-400">Student</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $receipt->payment->fee->student->user->name ?? $receipt->payment->student->user->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-slate-400">Invoice</span>
                    <span class="text-sm text-gray-900 dark:text-slate-200">{{ $receipt->payment->fee->invoice_number ?? '—' }}</span>
                </div>
            </div>

            <div class="border-b border-gray-200 dark:border-slate-700 py-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-slate-400">Payment Method</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method ?? '—')) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-slate-400">Transaction Reference</span>
                    <span class="text-sm text-gray-900 dark:text-slate-200">{{ $receipt->transaction_reference ?? '—' }}</span>
                </div>
            </div>

            <div class="flex justify-between items-center py-4">
                <span class="text-base font-semibold text-gray-900 dark:text-slate-200">Amount Paid</span>
                <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($receipt->amount, 2) }}</span>
            </div>

            @if($receipt->notes)
            <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mt-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase mb-1">Notes</p>
                <p class="text-sm text-gray-900 dark:text-slate-200">{{ $receipt->notes }}</p>
            </div>
            @endif

            <div class="mt-8 text-center">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-print mr-2"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
