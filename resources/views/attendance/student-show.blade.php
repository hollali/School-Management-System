<x-app-layout>
    @section('title', 'My Attendance')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">My Attendance</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">View your attendance records and history</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Attendance Rate</p>
                <p class="text-3xl font-bold {{ $summary['percentage'] >= 75 ? 'text-emerald-600' : 'text-red-600' }} mt-1">
                    {{ $summary['percentage'] }}%
                </p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Days</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $summary['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Present</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $summary['present'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Absent</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $summary['absent'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Late</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">{{ $summary['late'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Attendance History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                    <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($records as $record)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                                {{ $record->attendance?->attendance_date?->format('M d, Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">
                                {{ $record->attendance?->schoolClass?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                                {{ $record->attendance?->subject?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $colors = ['present'=>'bg-emerald-100 text-emerald-700','absent'=>'bg-red-100 text-red-700','late'=>'bg-amber-100 text-amber-700','excused'=>'bg-purple-100 text-purple-700'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$record->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $record->remarks ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
