<x-app-layout>
    @section('title', 'Fees')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Fees</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage student fees and track payments</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Fees" :data="$fees" searchable="true" searchPlaceholder="Search fees..." searchValue="{{ request('search') }}" searchRoute="{{ route('fees.index') }}">
            @if(Auth::user()->hasRole('Admin'))
            <x-slot name="actions">
                <a href="{{ route('fees.export-csv') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-file-csv mr-2"></i>
                    Export CSV
                </a>
                <button @click="$dispatch('open-modal', 'create-fee')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Fee
                </button>
            </x-slot>
            @endif

            <x-slot name="filters">
                <form action="{{ route('fees.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <select name="student_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->user->name }} ({{ $student->admission_number ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                    @foreach (request()->except('status', 'student_id', 'page') as $key => $value)
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Fee Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Due Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($fees as $fee)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition @if(in_array($fee->status, ['paid', 'cancelled'])) opacity-60 @endif">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $fee->student->user->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $fee->fee_type ?? $fee->invoice_number ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ number_format($fee->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $fee->due_date ? $fee->due_date->format('M d, Y') : '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($fee->status === 'paid') bg-emerald-100 text-emerald-700 dark:bg-green-900/30 dark:text-green-200
                            @elseif($fee->status === 'pending') bg-amber-100 text-amber-700 dark:bg-yellow-900/30 dark:text-yellow-200
                            @elseif($fee->status === 'partial') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200
                            @elseif($fee->status === 'overdue') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200
                            @elseif($fee->status === 'cancelled') bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-200
                            @else bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200 @endif">
                            {{ ucfirst($fee->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <button @click="
                            $dispatch('view-fee', @json([
                                'invoice_number' => $fee->invoice_number,
                                'student_name' => $fee->student->user->name ?? '—',
                                'amount' => number_format($fee->amount, 2),
                                'due_date' => $fee->due_date ? $fee->due_date->format('Y-m-d') : '—',
                                'status' => $fee->status,
                                'status_class' => match($fee->status) {
                                    'paid' => 'bg-emerald-100 text-emerald-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'partial' => 'bg-blue-100 text-blue-700',
                                    'overdue' => 'bg-red-100 text-red-700',
                                    'cancelled' => 'bg-gray-100 text-gray-500',
                                    default => 'bg-gray-100 text-gray-700',
                                },
                                'status_display' => ucfirst($fee->status),
                                'admission_number' => $fee->student->admission_number ?? '—',
                                'description' => $fee->description,
                            ]));
                            $dispatch('open-modal', 'view-fee');
                        " title="View" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        @if(Auth::user()->hasRole('Admin'))
                        <button @click="
                            $dispatch('edit-fee', @json([
                                'id' => $fee->id,
                                'student_id' => $fee->student_id,
                                'invoice_number' => $fee->invoice_number,
                                'amount' => $fee->amount,
                                'due_date' => $fee->due_date ? $fee->due_date->format('Y-m-d') : '',
                                'status' => $fee->status,
                                'description' => $fee->description,
                            ]));
                            $dispatch('open-modal', 'edit-fee');
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('fees.destroy', $fee) }}',
                            method: 'DELETE',
                            title: 'Delete Fee',
                            message: 'Delete this fee record? This action cannot be undone.',
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
                    <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No fee records found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-fee" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Fee') }}</h2>
                <button @click="$dispatch('close-modal', 'create-fee')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('fees.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                        <select name="student_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Select student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->user->name }} ({{ $student->admission_number ?? 'No admission #' }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Invoice Number</label>
                        <input type="text" name="invoice_number" value="{{ old('invoice_number') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                        @error('invoice_number')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4" required>
                        @error('amount')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                        @error('due_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status</label>
                        <select name="status"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="unpaid" {{ old('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ old('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'create-fee')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        {{ __('Create Fee') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-fee" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editFeeData()" @edit-fee.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Fee</h2>
                <button @click="$dispatch('close-modal', 'edit-fee')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/fees/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                        <select name="student_id" x-model="form.student_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Select student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->user->name }} ({{ $student->admission_number ?? 'No admission #' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Invoice Number</label>
                        <input type="text" name="invoice_number" x-model="form.invoice_number"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Amount</label>
                        <input type="number" step="0.01" name="amount" x-model="form.amount"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Due Date</label>
                        <input type="date" name="due_date" x-model="form.due_date"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status</label>
                        <select name="status" x-model="form.status"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="unpaid">Unpaid</option>
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" x-model="form.description" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'edit-fee')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        {{ __('Update Fee') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-fee" maxWidth="2xl" focusable>
        <div class="p-6" x-data="{ data: null }" @view-fee.window="data = $event.detail; $dispatch('open-modal', 'view-fee')">
            <template x-if="data">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Fee Details</h2>
                        <button @click="$dispatch('close-modal', 'view-fee')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Invoice Number</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.invoice_number || '—'"></p>
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
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Due Date</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.due_date || '—'"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="data.status_class">
                                <span x-text="data.status_display"></span>
                            </span>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Admission Number</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.admission_number || '—'"></p>
                        </div>
                        <div class="sm:col-span-2 bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Description</p>
                            <p class="text-gray-900 dark:text-slate-200" x-text="data.description || '—'"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </x-modal>

    <script>
        function editFeeData() {
            return {
                form: { id: '', student_id: '', invoice_number: '', amount: '', due_date: '', status: '', description: '' },
                load(data) {
                    this.form = { ...data };
                    this.$dispatch('open-modal', 'edit-fee');
                }
            };
        }
    </script>
</x-app-layout>
