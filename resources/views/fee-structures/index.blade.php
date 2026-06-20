<x-app-layout>
    @section('title', 'Fee Structures')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Fee Structures</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Define and manage fee structures for classes</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Fee Structures" :data="$structures">
            <x-slot name="actions">
                <button @click="$dispatch('open-modal', 'create-structure')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Structure
                </button>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Class</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Term / Year</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Items</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Active</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($structures as $structure)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition @if(!$structure->is_active) opacity-60 @endif">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $structure->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $structure->schoolClass->name ?? 'All Classes' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $structure->academic_term ?? '—' }} / {{ $structure->academic_year ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $structure->items->count() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-slate-200">${{ number_format($structure->items->sum('amount'), 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($structure->is_active) bg-emerald-100 text-emerald-700 dark:bg-green-900/30 dark:text-green-200
                            @else bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400 @endif">
                            {{ $structure->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <button @click="
                            $dispatch('edit-structure', {!! json_encode([
                                'id' => $structure->id,
                                'name' => $structure->name,
                                'class_id' => $structure->class_id,
                                'academic_term' => $structure->academic_term,
                                'academic_year' => $structure->academic_year,
                                'description' => $structure->description,
                                'is_active' => $structure->is_active,
                                'items' => $structure->items->map(fn($i) => ['fee_category_id' => $i->fee_category_id, 'amount' => $i->amount, 'description' => $i->description]),
                            ]) !!});
                            $dispatch('open-modal', 'edit-structure');
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('fee-structures.destroy', $structure) }}',
                            method: 'DELETE',
                            title: 'Delete Structure',
                            message: 'Delete this fee structure? This cannot be undone.',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })" title="Delete" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400 dark:text-slate-500">No fee structures found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-structure" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">New Fee Structure</h2>
                <button @click="$dispatch('close-modal', 'create-structure')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('fee-structures.store') }}" method="POST" x-data="{ items: [{ fee_category_id: '', amount: '', description: '' }] }">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                        <select name="class_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Year</label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', now()->year) }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Term</label>
                        <input type="text" name="academic_term" value="{{ old('academic_term') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="2"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200">Fee Items</h3>
                        <button @click="items.push({ fee_category_id: '', amount: '', description: '' })" type="button"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-sky-600 hover:text-white hover:bg-sky-600 border border-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-plus mr-1"></i> Add Item
                        </button>
                    </div>
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-3 p-3 bg-gray-50 dark:bg-slate-700/30 rounded-xl">
                            <div class="sm:col-span-2">
                                <select :name="`items[${index}][fee_category_id]`" x-model="item.fee_category_id" required
                                    class="block w-full rounded-lg border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2 px-3 bg-white">
                                    <option value="">Category</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="number" step="0.01" :name="`items[${index}][amount]`" x-model="item.amount" placeholder="Amount" required
                                    class="block w-full rounded-lg border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2 px-3">
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description" placeholder="Description"
                                    class="block w-full rounded-lg border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2 px-3">
                                <button @click="if (items.length > 1) items.splice(index, 1)" type="button"
                                    class="text-red-400 hover:text-red-600 p-1.5 shrink-0">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-end gap-4 mt-6">
                    <button @click="$dispatch('close-modal', 'create-structure')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        Create Structure
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-structure" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editStructureData()" @edit-structure.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Fee Structure</h2>
                <button @click="$dispatch('close-modal', 'edit-structure')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/fee-structures/${form.id}`">
                @csrf
                @method('PUT')
                <template x-if="form.id">
                    <div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                                <input type="text" name="name" x-model="form.name" required
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                                <select name="class_id" x-model="form.class_id"
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Year</label>
                                <input type="text" name="academic_year" x-model="form.academic_year" required
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Term</label>
                                <input type="text" name="academic_term" x-model="form.academic_term"
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="is_active" value="1" x-model="form.is_active"
                                        class="rounded border-gray-300 dark:border-slate-600 text-sky-600 focus:ring-sky-500">
                                    <span class="text-sm text-gray-700 dark:text-slate-300">Active</span>
                                </label>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                                <textarea name="description" x-model="form.description" rows="2"
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"></textarea>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-200">Fee Items</h3>
                                <button @click="form.items.push({ fee_category_id: '', amount: '', description: '' })" type="button"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-sky-600 hover:text-white hover:bg-sky-600 border border-sky-600 rounded-lg transition">
                                    <i class="fa-solid fa-plus mr-1"></i> Add Item
                                </button>
                            </div>
                            <template x-for="(item, index) in form.items" :key="index">
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-3 p-3 bg-gray-50 dark:bg-slate-700/30 rounded-xl">
                                    <div class="sm:col-span-2">
                                        <select :name="`items[${index}][fee_category_id]`" x-model="item.fee_category_id" required
                                            class="block w-full rounded-lg border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2 px-3 bg-white">
                                            <option value="">Category</option>
                                            @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <input type="number" step="0.01" :name="`items[${index}][amount]`" x-model="item.amount" placeholder="Amount" required
                                            class="block w-full rounded-lg border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2 px-3">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="`items[${index}][description]`" x-model="item.description" placeholder="Description"
                                            class="block w-full rounded-lg border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2 px-3">
                                        <button @click="if (form.items.length > 1) form.items.splice(index, 1)" type="button"
                                            class="text-red-400 hover:text-red-600 p-1.5 shrink-0">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <button @click="$dispatch('close-modal', 'edit-structure')" type="button"
                                class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                Update Structure
                            </button>
                        </div>
                    </div>
                </template>
            </form>
        </div>
    </x-modal>

    <script>
        function editStructureData() {
            return {
                form: { id: '', name: '', class_id: '', academic_term: '', academic_year: '', description: '', is_active: true, items: [] },
                load(data) {
                    this.form = { ...data };
                    this.$dispatch('open-modal', 'edit-structure');
                }
            };
        }
    </script>
</x-app-layout>
