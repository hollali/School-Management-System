<x-app-layout>
    @section('title', 'Finance Reports')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Finance Reports</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Generate financial reports</p>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <form method="GET" action="{{ route('finance.reports') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Report Type</label>
                    <select name="report_type" onchange="this.form.submit()"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="revenue" {{ $reportType == 'revenue' ? 'selected' : '' }}>Revenue Report</option>
                        <option value="outstanding" {{ $reportType == 'outstanding' ? 'selected' : '' }}>Outstanding Report</option>
                        <option value="class" {{ $reportType == 'class' ? 'selected' : '' }}>Class Summary</option>
                        <option value="student" {{ $reportType == 'student' ? 'selected' : '' }}>Student Statement</option>
                        <option value="category" {{ $reportType == 'category' ? 'selected' : '' }}>Category Breakdown</option>
                    </select>
                </div>

                @if(in_array($reportType, ['revenue']))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from', now()->startOfYear()->format('Y-m-d')) }}"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4">
                </div>
                @endif

                @if(in_array($reportType, ['outstanding']))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Class</label>
                    <select name="class_id"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(in_array($reportType, ['student']))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Student</label>
                    <select name="student_id" required
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <button type="submit" name="generate" value="1"
                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-chart-bar mr-2"></i> Generate
                    </button>
                </div>
            </form>
        </div>

        @if($reportData)
            @if($reportData['type'] === 'revenue')
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($reportData['revenue']['total_revenue'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $reportData['revenue']['total_payments'] ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Avg Payment</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">${{ number_format($reportData['revenue']['average_payment'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Collection Rate</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($reportData['revenue']['collection_rate'] ?? 0, 1) }}%</p>
                </div>
            </div>

            @if(($reportData['daily_totals'] ?? collect())->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Daily Totals</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Transactions</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($reportData['daily_totals'] as $daily)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-slate-200">{{ $daily['date'] }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-600">{{ $daily['count'] }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-emerald-600">${{ number_format($daily['total'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">All Payments</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($reportData['payments'] as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-slate-200">{{ $payment->fee->student->user->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-slate-200">${{ number_format($payment->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $payment->method ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '—' }}</td>
                                <td class="px-4 py-2 text-sm">{{ $payment->receipt?->receipt_number ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-8 text-gray-400">No payments found in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif($reportData['type'] === 'outstanding')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Outstanding</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">${{ number_format($reportData['outstanding']['total_outstanding'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Overdue Count</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $reportData['outstanding']['overdue_count'] ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Partial Count</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $reportData['outstanding']['partial_count'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Outstanding Invoices</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Paid</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Balance</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Due</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($reportData['invoices'] as $invoice)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-slate-200">{{ $invoice->student->user->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm font-mono text-gray-600">{{ $invoice->invoice_number ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-slate-200">${{ number_format($invoice->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-emerald-600">${{ number_format($invoice->paid_amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-red-600">${{ number_format($invoice->balance, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '—' }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($invoice->payment_status === 'overdue') bg-red-100 text-red-700
                                        @else bg-blue-100 text-blue-700 @endif">
                                        {{ ucfirst($invoice->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-8 text-gray-400">No outstanding invoices.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif($reportData['type'] === 'class')
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Class Financial Summary</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Class</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Students</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Invoiced</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Collected</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Outstanding</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Collection %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($reportData['classes'] as $class)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $class['class_name'] }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-600">{{ $class['students_count'] ?? 0 }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-slate-200">${{ number_format($class['total_invoiced'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-emerald-600">${{ number_format($class['total_collected'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-red-600">${{ number_format($class['total_outstanding'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900 dark:text-slate-200">{{ number_format($class['collection_rate'] ?? 0, 1) }}%</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-8 text-gray-400">No class data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif($reportData['type'] === 'student')
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-2">{{ $reportData['student']->user->name ?? 'Student' }}</h3>
                <p class="text-sm text-gray-500 mb-4">Admission: {{ $reportData['student']->admission_number ?? '—' }}</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 dark:bg-slate-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Invoiced</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-slate-200">${{ number_format($reportData['summary']['total_invoiced'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Paid</p>
                        <p class="text-xl font-bold text-emerald-600">${{ number_format($reportData['summary']['total_paid'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/30 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Outstanding</p>
                        <p class="text-xl font-bold text-red-600">${{ number_format($reportData['summary']['outstanding_balance'] ?? 0, 2) }}</p>
                    </div>
                </div>

                <h4 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-3">Invoices</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Paid</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Balance</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Due</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($reportData['invoices'] as $inv)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm font-mono text-gray-600">{{ $inv->invoice_number ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900">${{ number_format($inv->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-emerald-600">${{ number_format($inv->paid_amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-red-600">${{ number_format($inv->balance, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $inv->due_date ? $inv->due_date->format('Y-m-d') : '—' }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($inv->payment_status === 'paid') bg-emerald-100 text-emerald-700
                                        @elseif($inv->payment_status === 'partial') bg-blue-100 text-blue-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ ucfirst($inv->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-8 text-gray-400">No invoices.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif($reportData['type'] === 'category')
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-4">Category Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Items</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total Amount</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Discount</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Net Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($reportData['categories'] as $cat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $cat['name'] }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-600">{{ $cat['count'] }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900">${{ number_format($cat['total_amount'], 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-orange-600">${{ number_format($cat['total_discount'], 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-emerald-600">${{ number_format($cat['total_net'], 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-8 text-gray-400">No category data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @endif
    </div>
</x-app-layout>
