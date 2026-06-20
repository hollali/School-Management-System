<x-app-layout>
    @section('title', 'Payment Details')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Payment Details</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">View payment transaction details</p>
            </div>
            <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Student</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-slate-200">{{ $payment->student->user->name ?? $payment->fee->student->user->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Invoice</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-slate-200">{{ $payment->fee->invoice_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Amount</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-slate-200">${{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Method</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($payment->method === 'cash') bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200
                        @elseif($payment->method === 'mobile_money') bg-green-100 text-green-700
                        @elseif($payment->method === 'card') bg-blue-100 text-blue-700
                        @elseif($payment->method === 'bank_transfer') bg-purple-100 text-purple-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst(str_replace('_', ' ', $payment->method ?? '—')) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Reference</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200">{{ $payment->reference ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Paid At</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($payment->status === 'completed') bg-emerald-100 text-emerald-700
                        @elseif($payment->status === 'failed') bg-red-100 text-red-700
                        @elseif($payment->status === 'refunded') bg-gray-100 text-gray-500
                        @else bg-amber-100 text-amber-700 @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Receipt</p>
                    @if($payment->receipt)
                    <a href="{{ route('receipts.show', $payment->receipt) }}" class="text-sm text-sky-600 hover:text-sky-800 dark:text-sky-400">{{ $payment->receipt->receipt_number }}</a>
                    @else
                    <span class="text-sm text-gray-400">Not generated</span>
                    @endif
                </div>
                @if($payment->notes)
                <div class="md:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Notes</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200">{{ $payment->notes }}</p>
                </div>
                @endif
                @if($payment->parentProfile)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Paid By (Parent)</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200">{{ $payment->parentProfile->user->name ?? '—' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
