<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Receipt') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('receipts.edit', $receipt) }}"
                    class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ __('Edit') }}
                </a>
                <a onclick="window.print()" class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    {{ __('Print') }}
                </a>
                <a href="{{ route('receipts.index') }}"
                    class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden p-8">
                <div class="text-center border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Official Payment Receipt</p>
                </div>

                <div class="flex justify-between items-start mb-8">
                    <div>
                        <p class="text-sm text-gray-500">Receipt #</p>
                        <p class="text-lg font-bold text-gray-900">{{ $receipt->receipt_number ?? '—' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-bold text-gray-900">{{ $receipt->issued_at ? $receipt->issued_at->format('Y-m-d H:i') : '—' }}</p>
                    </div>
                </div>

                <div class="border-t border-b border-gray-200 py-4 mb-6">
                    <p class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Student Information</p>
                    <p class="text-base font-semibold text-gray-900">{{ $receipt->payment->fee->student->user->name ?? '—' }}</p>
                    <p class="text-sm text-gray-500">{{ $receipt->payment->fee->student->admission_number ?? '—' }}</p>
                </div>

                <table class="min-w-full divide-y divide-gray-100 mb-6">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3 text-sm text-gray-600">
                                Fee: {{ $receipt->payment->fee->invoice_number ?? '—' }}
                                @if($receipt->payment->fee->description)
                                    <br><span class="text-gray-500 text-xs">{{ $receipt->payment->fee->description }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right whitespace-nowrap font-semibold">{{ number_format($receipt->payment->amount, 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50/50 font-semibold">
                            <td class="px-4 py-3 text-sm text-gray-900">Total Paid</td>
                            <td class="px-4 py-3 text-lg font-bold text-emerald-600 text-right whitespace-nowrap">{{ number_format($receipt->payment->amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="flex justify-between items-center text-sm text-gray-500 border-t border-gray-200 pt-4">
                    <span>Payment Method: <strong class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $receipt->payment->method ?? '—')) }}</strong></span>
                    <span>Reference: <strong class="text-gray-900">{{ $receipt->payment->reference ?? '—' }}</strong></span>
                </div>

                @if($receipt->notes)
                    <div class="mt-6 bg-gray-50/50 rounded-xl p-4">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Notes</p>
                        <p class="text-sm text-gray-600">{{ $receipt->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
