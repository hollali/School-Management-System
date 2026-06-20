<x-app-layout>
    @section('title', 'Attendance')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Attendance Records</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Track and manage attendance records</p>
            </div>
            <div class="flex items-center gap-2">
                @can('mark', App\Models\Attendance::class)
                    <a href="{{ route('attendance.mark') }}"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-check-to-slot mr-2"></i>
                        Mark Attendance
                    </a>
                @endcan
                <a href="{{ route('attendance.dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-chart-simple mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Attendance" :data="$attendances" searchable="true"
            searchPlaceholder="Search by class..." searchValue="{{ request('search') }}"
            searchRoute="{{ route('attendance.index') }}">

            <x-slot name="filters">
                <div class="flex items-center gap-2 flex-wrap">
                    @if(isset($classes) && $classes->count() > 0)
                    <select name="class_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}{{ $class->section ? ' - '.$class->section : '' }}
                        </option>
                        @endforeach
                    </select>
                    @endif

                    @if(isset($subjects) && $subjects->count() > 0)
                    <select name="subject_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                    @endif

                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">

                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Class</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Teacher</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Students</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($attendances as $attendance)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">
                        {{ $attendance->schoolClass?->name ?? 'N/A' }}
                        {{ $attendance->schoolClass?->section ? ' - '.$attendance->schoolClass->section : '' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        {{ $attendance->subject?->name ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        {{ $attendance->attendance_date?->format('M d, Y') ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        {{ $attendance->teacher?->user?->name ?? $attendance->creator?->name ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        @php
                            $total = $attendance->records_count ?? $attendance->records->count();
                            $present = $attendance->records->where('status', 'present')->count();
                            $absent = $attendance->records->where('status', 'absent')->count();
                            $late = $attendance->records->where('status', 'late')->count();
                        @endphp
                        <span class="inline-flex items-center gap-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                {{ $present }} P
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                {{ $absent }} A
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                {{ $late }} L
                            </span>
                            <span class="text-xs text-gray-400">/ {{ $total }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <a href="{{ route('attendance.show', $attendance) }}"
                           title="View" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @can('update', $attendance)
                        <a href="{{ route('attendance.mark', ['class_id' => $attendance->class_id, 'attendance_date' => $attendance->attendance_date->format('Y-m-d'), 'subject_id' => $attendance->subject_id]) }}"
                           title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endcan
                        @can('delete', $attendance)
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('attendance.destroy', $attendance) }}',
                            method: 'DELETE',
                            title: 'Delete Attendance',
                            message: 'Delete this attendance record for {{ $attendance->schoolClass?->name ?? 'class' }} on {{ $attendance->attendance_date?->format('M d, Y') }}? This action cannot be undone.',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })" title="Delete" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No attendance records found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>
</x-app-layout>
