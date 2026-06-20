<x-app-layout>
    @section('title', 'Attendance Details')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Attendance Details</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                    {{ $attendance->schoolClass?->name ?? 'Class' }} &middot;
                    {{ $attendance->attendance_date?->format('l, F d, Y') ?? 'Unknown date' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                @can('mark', App\Models\Attendance::class)
                <a href="{{ route('attendance.mark', ['class_id' => $attendance->class_id, 'attendance_date' => $attendance->attendance_date->format('Y-m-d'), 'subject_id' => $attendance->subject_id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-pen-to-square mr-2"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('attendance.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @php
                $records = $attendance->records;
                $total = $records->count();
                $present = $records->where('status', 'present')->count();
                $absent = $records->where('status', 'absent')->count();
                $late = $records->where('status', 'late')->count();
                $excused = $records->where('status', 'excused')->count();
                $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Present</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $present }} <span class="text-sm font-normal text-gray-400">/ {{ $total }}</span></p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Absent</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $absent }} <span class="text-sm font-normal text-gray-400">/ {{ $total }}</span></p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Late</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $late }} <span class="text-sm font-normal text-gray-400">/ {{ $total }}</span></p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Attendance Rate</p>
                <p class="text-2xl font-bold text-sky-600 mt-1">{{ $percentage }}%</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Student Records</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                    <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($records as $index => $record)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $record->student?->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($record->student?->user?->name ?? 'S') }}"
                                         alt="" class="w-8 h-8 rounded-full ring-2 ring-gray-200 dark:ring-slate-600">
                                    <div>
                                        <p>{{ $record->student?->user?->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">{{ $record->student?->admission_number ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColors = [
                                        'present' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'absent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'late' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        'excused' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColors[$record->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $record->remarks ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-gray-400 dark:text-slate-500">No records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
