<x-app-layout>
    @section('title', 'Holidays')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Holidays</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage school holidays and non-attendance days</p>
            </div>
            @can('create', App\Models\Holiday::class)
            <a href="{{ route('holidays.create') }}"
               class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl transition">
                <i class="fa-solid fa-plus mr-2"></i>
                Add Holiday
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                @if(session('success'))
                <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/30 border-b border-emerald-200 dark:border-emerald-800">
                    <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
                @endif

                <x-data-table :items="$holidays" :columns="[
                    ['key' => 'name', 'label' => 'Holiday'],
                    ['key' => 'holiday_date', 'label' => 'Date', 'render' => fn($h) => $h->holiday_date->format('M d, Y')],
                    ['key' => 'day', 'label' => 'Day', 'render' => fn($h) => $h->holiday_date->format('l')],
                    ['key' => 'type', 'label' => 'Type', 'render' => fn($h) => ucfirst($h->type)],
                    ['key' => 'recurring', 'label' => 'Recurring', 'render' => fn($h) => $h->recurring ? '<span class="text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-repeat"></i> Yes</span>' : 'No'],
                    ['key' => 'description', 'label' => 'Description'],
                ]" :actions="[
                    'edit' => auth()->user()->hasRole('Admin') ? 'holidays.edit' : null,
                    'delete' => auth()->user()->hasRole('Admin') ? 'holidays.destroy' : null,
                ]" />

                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $holidays->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
