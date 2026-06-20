<x-app-layout>
    @section('title', 'Finance Dashboard')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Finance Dashboard</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Overview of financial activity</p>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        @if($role === 'admin')
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">${{ number_format($revenue['total_revenue'] ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $revenue['total_payments'] ?? 0 }} payments</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Outstanding</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">${{ number_format($outstanding['total_outstanding'] ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $outstanding['overdue_count'] ?? 0 }} overdue</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Collection Rate</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($revenue['collection_rate'] ?? 0, 1) }}%</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Period</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-slate-200 mt-1">{{ $startDate }} to {{ $endDate }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Recent Payments</h3>
                    <div class="space-y-3">
                        @forelse($recentPayments as $payment)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-slate-700 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $payment->fee->student->user->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $payment->method ?? '—' }} · {{ $payment->paid_at ? $payment->paid_at->format('M d') : '' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">${{ number_format($payment->amount, 2) }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No recent payments</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Overdue Invoices</h3>
                    <div class="space-y-3">
                        @forelse($overdueInvoices as $invoice)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-slate-700 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $invoice->student->user->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Due {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-red-600 dark:text-red-400">${{ number_format($invoice->balance, 2) }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No overdue invoices</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($monthlyRevenue->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Monthly Revenue (Last 12 Months)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Month</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($monthlyRevenue as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-slate-200">{{ $item->month }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-emerald-600">${{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Class Financial Summary</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Class</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total Invoiced</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total Collected</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Outstanding</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Collection %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($classSummaries as $summary)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $summary['class']->name }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-slate-200">${{ number_format($summary['total_invoiced'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-emerald-600">${{ number_format($summary['total_collected'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-red-600">${{ number_format($summary['total_outstanding'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900 dark:text-slate-200">{{ number_format($summary['collection_rate'] ?? 0, 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($role === 'student' && isset($summary))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Invoiced</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-slate-200 mt-1">${{ number_format($summary['total_invoiced'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Paid</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($summary['total_paid'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Outstanding</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">${{ number_format($summary['outstanding_balance'] ?? 0, 2) }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200">Invoices</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Paid</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $invoice->invoice_number ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-slate-200">${{ number_format($invoice->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-emerald-600">${{ number_format($invoice->paid_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-red-600">${{ number_format($invoice->balance, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($invoice->payment_status === 'paid') bg-emerald-100 text-emerald-700
                                        @elseif($invoice->payment_status === 'partial') bg-blue-100 text-blue-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ ucfirst($invoice->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-12 text-gray-400">No invoices found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $invoices->links() }}
                </div>
            </div>

        @elseif($role === 'parent' && isset($childrenSummaries))
            @foreach($childrenSummaries as $child)
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">{{ $child['student']->user->name ?? 'Student' }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Invoiced</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-slate-200">${{ number_format($child['summary']['total_invoiced'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Paid</p>
                        <p class="text-xl font-bold text-emerald-600">${{ number_format($child['summary']['total_paid'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Outstanding</p>
                        <p class="text-xl font-bold text-red-600">${{ number_format($child['summary']['outstanding_balance'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            @endforeach

            @if(isset($outstandingInvoices) && $outstandingInvoices->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Outstanding Invoices</h3>
                <div class="space-y-3">
                    @foreach($outstandingInvoices as $invoice)
                    <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-200 dark:border-amber-700/30">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->student->user->name }}</p>
                            <p class="text-xs text-gray-500">#{{ $invoice->invoice_number }} — Due {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <p class="text-sm font-bold text-amber-700">${{ number_format($invoice->balance, 2) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($recentPayments) && $recentPayments->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Recent Payments</h3>
                <div class="space-y-3">
                    @foreach($recentPayments as $payment)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $payment->fee->student->user->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->method }} · {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : '' }}</p>
                        </div>
                        <p class="text-sm font-semibold text-emerald-600">${{ number_format($payment->amount, 2) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif
    </div>
</x-app-layout>
