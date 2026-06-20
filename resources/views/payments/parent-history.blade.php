<x-app-layout>
    @section('title', 'My Payment History')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">My Payment History</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">View your payment transactions and outstanding invoices</p>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        @if($outstandingInvoices->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Outstanding Invoices</h3>
            <div class="space-y-3">
                @foreach($outstandingInvoices as $invoice)
                <div class="flex items-center justify-between p-4 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-200 dark:border-amber-700/30">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $invoice->student->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Invoice #{{ $invoice->invoice_number ?? 'N/A' }} — Due: {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-amber-700 dark:text-amber-400">${{ number_format($invoice->balance, 2) }}</p>
                        <p class="text-xs text-amber-600 dark:text-amber-500">{{ ucfirst($invoice->payment_status) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200">Payment History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $payment->student->user->name ?? $payment->fee->student->user->name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $payment->fee->invoice_number ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $payment->method ?? '—')) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->status === 'completed') bg-emerald-100 text-emerald-700
                                    @elseif($payment->status === 'failed') bg-red-100 text-red-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                @if($payment->receipt)
                                <a href="{{ route('receipts.show', $payment->receipt) }}" class="text-sky-600 hover:text-sky-800 dark:text-sky-400">View</a>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400 dark:text-slate-500">No payments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
