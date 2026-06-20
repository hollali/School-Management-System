<x-app-layout>
    @section('title', 'Staff Attendance Details')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Staff Attendance Details</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                    {{ $staffAttendance->teacher?->user?->name ?? 'Unknown' }} &middot;
                    {{ $staffAttendance->attendance_date?->format('l, F d, Y') ?? 'N/A' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $staffAttendance)
                <a href="{{ route('staff-attendance.edit', $staffAttendance) }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-pen-to-square mr-2"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('staff-attendance.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Teacher</p>
                        <div class="flex items-center gap-3 mt-2">
                            <img src="{{ $staffAttendance->teacher?->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($staffAttendance->teacher?->user?->name ?? 'T') }}"
                                 alt="" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-slate-200">
                                    {{ $staffAttendance->teacher?->user?->name ?? 'Unknown' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-slate-400">
                                    {{ $staffAttendance->teacher?->employee_number ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Date</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-slate-200 mt-2">
                            {{ $staffAttendance->attendance_date?->format('l, F d, Y') ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Status</p>
                        @php
                            $colors = ['present'=>'bg-emerald-100 text-emerald-700','absent'=>'bg-red-100 text-red-700','late'=>'bg-amber-100 text-amber-700','on_leave'=>'bg-blue-100 text-blue-700','excused'=>'bg-purple-100 text-purple-700'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2 {{ $colors[$staffAttendance->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst(str_replace('_', ' ', $staffAttendance->status)) }}
                        </span>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Recorded By</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-slate-200 mt-2">
                            {{ $staffAttendance->marker?->name ?? 'System' }}
                        </p>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Check In</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-slate-200 mt-2">
                            {{ $staffAttendance->check_in?->format('h:i A') ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                        <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Check Out</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-slate-200 mt-2">
                            {{ $staffAttendance->check_out?->format('h:i A') ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6 sm:col-span-2">
                        <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Remarks</p>
                        <p class="text-gray-900 dark:text-slate-200 mt-2">{{ $staffAttendance->remarks ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
