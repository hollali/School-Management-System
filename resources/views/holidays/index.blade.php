<x-app-layout>
    @section('title', 'Holidays')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Holidays</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage school holidays and non-attendance days</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Holidays" :data="$holidays">
            <x-slot name="actions">
                @can('create', App\Models\Holiday::class)
                <a href="{{ route('holidays.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Holiday
                </a>
                @endcan
            </x-slot>

            @if(session('success'))
            <x-slot name="header">
                <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/30 border-b border-emerald-200 dark:border-emerald-800">
                    <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </x-slot>
            @endif

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Holiday</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Day</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Recurring</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($holidays as $holiday)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $holiday->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $holiday->holiday_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $holiday->holiday_date->format('l') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($holiday->type === 'public') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-200
                            @elseif($holiday->type === 'school') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200
                            @else bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200 @endif">
                            {{ ucfirst($holiday->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        @if($holiday->recurring)
                            <span class="text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-repeat"></i> Yes</span>
                        @else
                            No
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300 max-w-xs truncate">{{ $holiday->description ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        @can('update', $holiday)
                        <a href="{{ route('holidays.edit', $holiday) }}" title="Edit"
                           class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endcan
                        @can('delete', $holiday)
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('holidays.destroy', $holiday) }}',
                            method: 'DELETE',
                            title: 'Delete Holiday',
                            message: 'Delete {{ $holiday->name }}? This cannot be undone.',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })" title="Delete"
                           class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400 dark:text-slate-500">No holidays found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>
</x-app-layout>
