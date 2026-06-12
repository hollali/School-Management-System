<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Receipt') }}</h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasRole('Admin'))
                    <a href="{{ route('receipts.edit', $receipt) }}" title="Edit"
                        class="inline-flex items-center justify-center w-9 h-9 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endif
                <a onclick="window.print()" title="Print"
                    class="inline-flex items-center justify-center w-9 h-9 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                </a>
                <a href="{{ route('receipts.index') }}" title="Back"
                    class="inline-flex items-center justify-center w-9 h-9 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition">
                    <i class="fa-solid fa-arrow-left"></i>
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
