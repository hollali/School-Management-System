<x-app-layout>
    @section('title', 'Receipts')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Receipts</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">View and manage payment receipts</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Receipts" :data="$receipts" searchable="true" searchPlaceholder="Search receipts..." searchValue="{{ request('search') }}" searchRoute="{{ route('receipts.index') }}">
            @if(Auth::user()->hasRole('Admin'))
            <x-slot name="actions">
                <a href="{{ route('receipts.export-csv') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-file-csv mr-2"></i>
                    Export CSV
                </a>
            </x-slot>
            @endif

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Receipt Number</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Payment Amount</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($receipts as $receipt)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $receipt->receipt_number ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $receipt->payment->fee->student->user->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ number_format($receipt->payment->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $receipt->issued_at ? $receipt->issued_at->format('M d, Y') : '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <button onclick="window.print()" title="Print" class="inline-flex items-center justify-center w-8 h-8 text-gray-500 dark:text-slate-400 hover:text-white hover:bg-gray-500 rounded-lg transition">
                            <i class="fa-solid fa-print"></i>
                        </button>
                        <button @click="
                            $dispatch('view-receipt', @json([
                                'receipt_number' => $receipt->receipt_number ?? '—',
                                'payment_ref' => $receipt->payment->reference ?? '—',
                                'student_name' => $receipt->payment->fee->student->user->name ?? '—',
                                'amount' => number_format($receipt->payment->amount, 2),
                                'issued_at' => $receipt->issued_at ? $receipt->issued_at->format('Y-m-d H:i') : '—',
                                'method' => $receipt->payment->method,
                                'method_class' => match($receipt->payment->method) {
                                    'cash' => 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200',
                                    'mobile_money' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200',
                                    'card' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                                    'bank_transfer' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-200',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200',
                                },
                                'method_display' => ucfirst(str_replace('_', ' ', $receipt->payment->method ?? '—')),
                                'reference' => $receipt->payment->reference,
                                'notes' => $receipt->notes,
                            ]));
                            $dispatch('open-modal', 'view-receipt');
                        " title="View" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        @if(Auth::user()->hasRole('Admin'))
                        <button @click="
                            $dispatch('edit-receipt', @json([
                                'id' => $receipt->id,
                                'payment_id' => $receipt->payment_id,
                                'receipt_number' => $receipt->receipt_number,
                                'issued_at' => $receipt->issued_at ? $receipt->issued_at->format('Y-m-d\TH:i') : '',
                                'notes' => $receipt->notes,
                            ]));
                            $dispatch('open-modal', 'edit-receipt');
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('receipts.destroy', $receipt) }}',
                            method: 'DELETE',
                            title: 'Delete Receipt',
                            message: 'Delete this receipt? This action cannot be undone.',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })" title="Delete" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">No receipts found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-receipt" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Receipt') }}</h2>
                <button @click="$dispatch('close-modal', 'create-receipt')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('receipts.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Payment</label>
                        <select name="payment_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select payment</option>
                            @foreach($payments as $payment)
                                <option value="{{ $payment->id }}" {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                    {{ $payment->fee->student->user->name ?? 'N/A' }} — {{ number_format($payment->amount, 2) }} ({{ $payment->reference ?? 'No ref' }})
                                </option>
                            @endforeach
                        </select>
                        @error('payment_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Receipt Number</label>
                        <input type="text" name="receipt_number" value="{{ old('receipt_number') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                        @error('receipt_number')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Issued At</label>
                        <input type="datetime-local" name="issued_at" value="{{ old('issued_at') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                        @error('issued_at')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Notes</label>
                        <textarea name="notes" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'create-receipt')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        {{ __('Create Receipt') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-receipt" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editReceiptData()" @edit-receipt.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Receipt</h2>
                <button @click="$dispatch('close-modal', 'edit-receipt')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/receipts/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Payment</label>
                        <select name="payment_id" x-model="form.payment_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select payment</option>
                            @foreach($payments as $payment)
                                <option value="{{ $payment->id }}">
                                    {{ $payment->fee->student->user->name ?? 'N/A' }} — {{ number_format($payment->amount, 2) }} ({{ $payment->reference ?? 'No ref' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Receipt Number</label>
                        <input type="text" name="receipt_number" x-model="form.receipt_number"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Issued At</label>
                        <input type="datetime-local" name="issued_at" x-model="form.issued_at"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Notes</label>
                        <textarea name="notes" x-model="form.notes" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'edit-receipt')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        {{ __('Update Receipt') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-receipt" maxWidth="2xl" focusable>
        <div class="p-6" x-data="{ data: null }" @view-receipt.window="data = $event.detail; $dispatch('open-modal', 'view-receipt')">
            <template x-if="data">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Receipt Details</h2>
                        <button @click="$dispatch('close-modal', 'view-receipt')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Receipt Number</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.receipt_number || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Payment Ref</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.payment_ref || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Student</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.student_name || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Amount</p>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400" x-text="data.amount || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Issued Date</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.issued_at || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Payment Method</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="data.method_class">
                                <span x-text="data.method_display"></span>
                            </span>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Reference</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.reference || '—'"></p>
                        </div>
                        <div class="sm:col-span-2 bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6" x-show="data.notes">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Notes</p>
                            <p class="text-gray-900 dark:text-slate-200" x-text="data.notes || '—'"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </x-modal>

    <script>
        function editReceiptData() {
            return {
                form: { id: '', payment_id: '', receipt_number: '', issued_at: '', notes: '' },
                load(data) {
                    this.form = { ...data };
                    this.$dispatch('open-modal', 'edit-receipt');
                }
            };
        }
    </script>
</x-app-layout>
