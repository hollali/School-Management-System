<x-app-layout>
    @section('title', 'Discounts & Scholarships')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Discounts & Scholarships</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage discounts, scholarships, and fee waivers</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Discounts" :data="$discounts">
            <x-slot name="actions">
                <button @click="$dispatch('open-modal', 'create-discount')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Discount
                </button>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Applied To</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Value</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Valid Until</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($discounts as $discount)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($discount->type === 'scholarship') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-200
                            @elseif($discount->type === 'waiver') bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-200
                            @else bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200 @endif">
                            {{ ucfirst($discount->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $discount->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        @if($discount->student)
                            {{ $discount->student->user->name ?? 'Student #'.$discount->student_id }}
                        @elseif($discount->schoolClass)
                            {{ $discount->schoolClass->name }} (All)
                        @else
                            <span class="text-gray-400">All Students</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-slate-200">
                        @if($discount->application === 'percentage')
                            {{ $discount->value }}%
                        @else
                            ${{ number_format($discount->value, 2) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        {{ $discount->valid_until ? $discount->valid_until->format('M d, Y') : '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <button @click="
                            $dispatch('edit-discount', {!! json_encode([
                                'id' => $discount->id,
                                'student_id' => $discount->student_id,
                                'class_id' => $discount->class_id,
                                'type' => $discount->type,
                                'name' => $discount->name,
                                'description' => $discount->description,
                                'application' => $discount->application,
                                'value' => $discount->value,
                                'valid_from' => $discount->valid_from ? $discount->valid_from->format('Y-m-d') : '',
                                'valid_until' => $discount->valid_until ? $discount->valid_until->format('Y-m-d') : '',
                            ]) !!});
                            $dispatch('open-modal', 'edit-discount');
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('discounts.destroy', $discount) }}',
                            method: 'DELETE',
                            title: 'Delete Discount',
                            message: 'Delete this discount? This cannot be undone.',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })" title="Delete" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No discounts found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-discount" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">New Discount</h2>
                <button @click="$dispatch('close-modal', 'create-discount')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('discounts.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Type</label>
                        <select name="type" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="discount" {{ old('type') == 'discount' ? 'selected' : '' }}>Discount</option>
                            <option value="scholarship" {{ old('type') == 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                            <option value="waiver" {{ old('type') == 'waiver' ? 'selected' : '' }}>Waiver</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student (Optional)</label>
                        <select name="student_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">All / Class-based</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->user->name }} ({{ $student->admission_number ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class (Optional)</label>
                        <select name="class_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Application</label>
                        <select name="application" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="percentage" {{ old('application') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('application') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Value</label>
                        <input type="number" step="0.01" name="value" value="{{ old('value') }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Valid From</label>
                        <input type="date" name="valid_from" value="{{ old('valid_from') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Valid Until</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="2"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'create-discount')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        Create Discount
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-discount" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editDiscountData()" @edit-discount.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Discount</h2>
                <button @click="$dispatch('close-modal', 'edit-discount')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/discounts/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Type</label>
                        <select name="type" x-model="form.type" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="discount">Discount</option>
                            <option value="scholarship">Scholarship</option>
                            <option value="waiver">Waiver</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                        <select name="student_id" x-model="form.student_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">All / Class-based</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                        <select name="class_id" x-model="form.class_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">All</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Application</label>
                        <select name="application" x-model="form.application" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Value</label>
                        <input type="number" step="0.01" name="value" x-model="form.value" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Valid From</label>
                        <input type="date" name="valid_from" x-model="form.valid_from"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Valid Until</label>
                        <input type="date" name="valid_until" x-model="form.valid_until"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" x-model="form.description" rows="2"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'edit-discount')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        Update Discount
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        function editDiscountData() {
            return {
                form: { id: '', student_id: '', class_id: '', type: 'discount', name: '', description: '', application: 'percentage', value: '', valid_from: '', valid_until: '' },
                load(data) {
                    this.form = { ...data };
                    this.$dispatch('open-modal', 'edit-discount');
                }
            };
        }
    </script>
</x-app-layout>
