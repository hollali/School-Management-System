<x-app-layout>
    @section('title', 'Fee Details')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Fee Details</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Invoice #{{ $fee->invoice_number }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('fees.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Student</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-slate-200">{{ $fee->student->user->name ?? '—' }}</p>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Admission: {{ $fee->student->admission_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Invoice Number</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-slate-200">{{ $fee->invoice_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($fee->payment_status === 'paid') bg-emerald-100 text-emerald-700 dark:bg-green-900/30 dark:text-green-200
                        @elseif($fee->payment_status === 'partial') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200
                        @elseif($fee->payment_status === 'overdue') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200
                        @else bg-amber-100 text-amber-700 dark:bg-yellow-900/30 dark:text-yellow-200 @endif">
                        {{ ucfirst($fee->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Amount</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200">${{ number_format($fee->amount, 2) }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Paid Amount</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($fee->paid_amount, 2) }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Balance</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($fee->balance, 2) }}</p>
            </div>
        </div>

        @if($fee->items->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200">Invoice Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($fee->items as $item)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-slate-200">{{ $item->category->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $item->description ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-slate-200 text-right">${{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-slate-200">Total</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-slate-200 text-right">${{ number_format($fee->items->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        @if($fee->payments->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200">Payments</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($fee->payments as $payment)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-slate-200">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $payment->method ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $payment->reference ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : '—' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($payment->receipt)
                                <a href="{{ route('receipts.show', $payment->receipt) }}" class="text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300">{{ $payment->receipt->receipt_number }}</a>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($fee->description)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Description</p>
            <p class="text-sm text-gray-900 dark:text-slate-200">{{ $fee->description }}</p>
        </div>
        @endif
    </div>
</x-app-layout>
