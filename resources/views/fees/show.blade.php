<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Fee Details') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('fees.edit', $fee) }}"
                    class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('fees.index') }}"
                    class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Invoice Number</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $fee->invoice_number ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Student</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $fee->student->user->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Amount</p>
                        <p class="text-lg font-bold text-emerald-600">{{ number_format($fee->amount, 2) }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Due Date</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $fee->due_date ? $fee->due_date->format('Y-m-d') : '—' }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($fee->status === 'paid') bg-emerald-100 text-emerald-700
                            @elseif($fee->status === 'pending') bg-amber-100 text-amber-700
                            @elseif($fee->status === 'partial') bg-blue-100 text-blue-700
                            @elseif($fee->status === 'overdue') bg-red-100 text-red-700
                            @elseif($fee->status === 'cancelled') bg-gray-100 text-gray-500
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($fee->status) }}
                        </span>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Admission Number</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $fee->student->admission_number ?? '—' }}</p>
                    </div>
                    <div class="sm:col-span-2 bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Description</p>
                        <p class="text-gray-900">{{ $fee->description ?? '—' }}</p>
                    </div>
                </div>

                @if($fee->payments->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($fee->payments as $payment)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($payment->amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $payment->method ?? '—' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '—' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($payment->status === 'completed') bg-emerald-100 text-emerald-700
                                                    @elseif($payment->status === 'failed') bg-red-100 text-red-700
                                                    @elseif($payment->status === 'refunded') bg-gray-100 text-gray-500
                                                    @else bg-amber-100 text-amber-700 @endif">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
