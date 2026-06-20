<x-app-layout>
    @section('title', 'Fee Categories')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Fee Categories</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage fee categories (tuition, library, sports, etc.)</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Fee Categories" :data="$categories">
            <x-slot name="actions">
                <button @click="$dispatch('open-modal', 'create-category')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Category
                </button>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Code</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Active</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition @if(!$category->is_active) opacity-60 @endif">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $category->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-slate-300">{{ $category->code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300 max-w-xs truncate">{{ $category->description ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($category->is_active) bg-emerald-100 text-emerald-700 dark:bg-green-900/30 dark:text-green-200
                            @else bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400 @endif">
                            {{ $category->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <button @click="
                            $dispatch('edit-category', {!! json_encode([
                                'id' => $category->id,
                                'name' => $category->name,
                                'code' => $category->code,
                                'description' => $category->description,
                                'is_active' => $category->is_active,
                            ]) !!});
                            $dispatch('open-modal', 'edit-category');
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('fee-categories.destroy', $category) }}',
                            method: 'DELETE',
                            title: 'Delete Category',
                            message: 'Delete this fee category?',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })" title="Delete" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">No fee categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-category" maxWidth="lg" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">New Fee Category</h2>
                <button @click="$dispatch('close-modal', 'create-category')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('fee-categories.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Code</label>
                        <input type="text" name="code" value="{{ old('code') }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'create-category')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-category" maxWidth="lg" focusable>
        <div class="p-6" x-data="editCategoryData()" @edit-category.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Fee Category</h2>
                <button @click="$dispatch('close-modal', 'edit-category')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/fee-categories/${form.id}`">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Code</label>
                        <input type="text" name="code" x-model="form.code" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" x-model="form.description" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" x-model="form.is_active"
                                class="rounded border-gray-300 dark:border-slate-600 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm text-gray-700 dark:text-slate-300">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'edit-category')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        function editCategoryData() {
            return {
                form: { id: '', name: '', code: '', description: '', is_active: true },
                load(data) {
                    this.form = { ...data };
                    this.form.is_active = Boolean(data.is_active);
                    this.$dispatch('open-modal', 'edit-category');
                }
            };
        }
    </script>
</x-app-layout>
