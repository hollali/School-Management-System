@section('title', 'Academic Terms')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Academic Terms</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage academic periods and terms</p>
            </div>
            @if(Auth::user()->hasRole('Admin'))
                <button @click="$dispatch('open-modal', 'create-term')" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus"></i> New Term
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Academic Terms" :data="$terms">
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">End Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($terms as $term)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $term->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $term->start_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $term->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if($term->is_current)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Current</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-1">
                            <button @click="$dispatch('open-modal', 'edit-term-{{ $term->id }}')" class="inline-flex items-center justify-center w-8 h-8 text-amber-600 hover:text-white hover:bg-amber-600 rounded-lg transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button @click="$dispatch('set-confirmation', {
                                action: '{{ route('academic-terms.destroy', $term) }}',
                                method: 'DELETE',
                                title: 'Delete Term',
                                message: 'Delete this academic term?',
                                confirmLabel: 'Delete',
                                confirmClass: 'bg-red-600 hover:bg-red-700'
                            })" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition" title="Delete">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">No terms defined.</td></tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-term" maxWidth="lg" focusable>
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-6">New Academic Term</h2>
            <form action="{{ route('academic-terms.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Start Date</label>
                            <input type="date" name="start_date" required
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">End Date</label>
                            <input type="date" name="end_date" required
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                        </div>
                    </div>
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer">
                        <input type="checkbox" name="is_current" value="1" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Set as current term</span>
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="$dispatch('close-modal', 'create-term')" type="button" class="px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Create</button>
                </div>
            </form>
        </div>
    </x-modal>

    @foreach($terms as $term)
        <x-modal name="edit-term-{{ $term->id }}" maxWidth="lg" focusable>
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-6">Edit Term</h2>
                <form action="{{ route('academic-terms.update', $term) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                            <input type="text" name="name" value="{{ $term->name }}" required
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Start Date</label>
                                <input type="date" name="start_date" value="{{ $term->start_date->format('Y-m-d') }}" required
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">End Date</label>
                                <input type="date" name="end_date" value="{{ $term->end_date->format('Y-m-d') }}" required
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            </div>
                        </div>
                        <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer">
                            <input type="checkbox" name="is_current" value="1" @checked($term->is_current) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm text-gray-700 dark:text-slate-300">Set as current term</span>
                        </label>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'edit-term-{{ $term->id }}')" type="button" class="px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Update</button>
                    </div>
                </form>
            </div>
        </x-modal>
    @endforeach
</x-app-layout>
