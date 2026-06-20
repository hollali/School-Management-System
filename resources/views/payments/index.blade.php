<x-app-layout>
    @section('title', 'Payments')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Payments</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Record and manage payment transactions</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Payments" :data="$payments" searchable="true" searchPlaceholder="Search payments..." searchValue="{{ request('search') }}" searchRoute="{{ route('payments.index') }}">
            @if(Auth::user()->hasRole('Admin'))
            <x-slot name="actions">
                <button @click="$dispatch('open-modal', 'create-payment')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Payment
                </button>
            </x-slot>
            @endif

            <x-slot name="filters">
                <form action="{{ route('payments.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="method" onchange="this.form.submit()" class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ request('method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="card" {{ request('method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ request('method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="online" {{ request('method') == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                    <select name="fee_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Fees</option>
                        @foreach($fees as $fee)
                        <option value="{{ $fee->id }}" {{ request('fee_id') == $fee->id ? 'selected' : '' }}>{{ $fee->invoice_number ?? 'Fee #'.$fee->id }} — {{ $fee->student->user->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                    @foreach (request()->except('method', 'fee_id', 'page') as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}" />
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                        @endif
                    @endforeach
                </form>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Fee</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Amount Paid</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Payment Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Method</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Reference</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition @if($payment->status === 'completed') opacity-60 @endif">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $payment->fee->student->user->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $payment->fee->invoice_number ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($payment->method === 'cash') bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200
                            @elseif($payment->method === 'mobile_money') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200
                            @elseif($payment->method === 'card') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200
                            @elseif($payment->method === 'bank_transfer') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-200
                            @else bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200 @endif">
                            {{ ucfirst(str_replace('_', ' ', $payment->method ?? '—')) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $payment->reference ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        @php
                            $_pm = $payment->method;
                            $_pm_class = match($_pm) {
                                'cash' => 'bg-gray-100 text-gray-700',
                                'mobile_money' => 'bg-green-100 text-green-700',
                                'card' => 'bg-blue-100 text-blue-700',
                                'bank_transfer' => 'bg-purple-100 text-purple-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $_ps = $payment->status;
                            $_ps_class = match($_ps) {
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'failed' => 'bg-red-100 text-red-700',
                                'refunded' => 'bg-gray-100 text-gray-500',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <button @click="
                            $dispatch('view-payment', {!! json_encode([
                                'fee_invoice' => $payment->fee->invoice_number ?? '—',
                                'student_name' => $payment->fee->student->user->name ?? '—',
                                'amount' => number_format($payment->amount, 2),
                                'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '—',
                                'method' => $_pm,
                                'method_class' => $_pm_class,
                                'method_display' => ucfirst(str_replace('_', ' ', $_pm ?? '—')),
                                'reference' => $payment->reference,
                                'status' => $_ps,
                                'status_class' => $_ps_class,
                                'status_display' => ucfirst($_ps),
                            ]) !!});
                            $dispatch('open-modal', 'view-payment');
                        " title="View" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        @if(Auth::user()->hasRole('Admin'))
                        <button @click="
                            $dispatch('edit-payment', {!! json_encode([
                                'id' => $payment->id,
                                'fee_id' => $payment->fee_id,
                                'amount' => $payment->amount,
                                'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d\TH:i') : '',
                                'method' => $payment->method,
                                'reference' => $payment->reference,
                                'status' => $payment->status,
                            ]) !!});
                            $dispatch('open-modal', 'edit-payment');
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('payments.destroy', $payment) }}',
                            method: 'DELETE',
                            title: 'Delete Payment',
                            message: 'Delete this payment? This action cannot be undone.',
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
                    <td colspan="7" class="text-center py-12 text-gray-400 dark:text-slate-500">No payment records found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-payment" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Payment') }}</h2>
                <button @click="$dispatch('close-modal', 'create-payment')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Fee</label>
                        <select name="fee_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Select fee</option>
                            @foreach($fees as $fee)
                                <option value="{{ $fee->id }}" {{ old('fee_id') == $fee->id ? 'selected' : '' }}>
                                    {{ $fee->student->user->name ?? 'N/A' }} — {{ number_format($fee->amount, 2) }} ({{ $fee->invoice_number ?? 'No invoice' }})
                                </option>
                            @endforeach
                        </select>
                        @error('fee_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4" required>
                        @error('amount')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Paid At</label>
                        <input type="datetime-local" name="paid_at" value="{{ old('paid_at') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                        @error('paid_at')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Method</label>
                        <select name="method"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Select method</option>
                            <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ old('method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="mobile_money" {{ old('method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="card" {{ old('method') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="online" {{ old('method') == 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                        @error('method')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Reference</label>
                        <input type="text" name="reference" value="{{ old('reference') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                        @error('reference')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status</label>
                        <select name="status"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                        @error('status')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'create-payment')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        {{ __('Record Payment') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-payment" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editPaymentData()" @edit-payment.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Payment</h2>
                <button @click="$dispatch('close-modal', 'edit-payment')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/payments/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Fee</label>
                        <select name="fee_id" x-model="form.fee_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Select fee</option>
                            @foreach($fees as $fee)
                                <option value="{{ $fee->id }}">
                                    {{ $fee->student->user->name ?? 'N/A' }} — {{ number_format($fee->amount, 2) }} ({{ $fee->invoice_number ?? 'No invoice' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Amount</label>
                        <input type="number" step="0.01" name="amount" x-model="form.amount"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Paid At</label>
                        <input type="datetime-local" name="paid_at" x-model="form.paid_at"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Method</label>
                        <select name="method" x-model="form.method"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Select method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Reference</label>
                        <input type="text" name="reference" x-model="form.reference"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status</label>
                        <select name="status" x-model="form.status"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'edit-payment')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        {{ __('Update Payment') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-payment" maxWidth="2xl" focusable>
        <div class="p-6" x-data="{ data: null }" @view-payment.window="data = $event.detail; $dispatch('open-modal', 'view-payment')">
            <template x-if="data">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Payment Details</h2>
                        <button @click="$dispatch('close-modal', 'view-payment')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Fee Invoice</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.fee_invoice || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Student</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.student_name || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Amount</p>
                            <p class="text-lg font-bold text-emerald-600" x-text="data.amount || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Paid At</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.paid_at || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Method</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="data.method_class">
                                <span x-text="data.method_display"></span>
                            </span>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Reference</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.reference || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="data.status_class">
                                <span x-text="data.status_display"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </x-modal>

    <script>
        function editPaymentData() {
            return {
                form: { id: '', fee_id: '', amount: '', paid_at: '', method: '', reference: '', status: '' },
                load(data) {
                    this.form = { ...data };
                    this.$dispatch('open-modal', 'edit-payment');
                }
            };
        }
    </script>
</x-app-layout>
