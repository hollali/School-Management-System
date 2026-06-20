<x-app-layout>
    @section('title', 'Staff Attendance')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Staff Attendance</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Track teacher and staff attendance</p>
            </div>
            <div class="flex items-center gap-2">
                @if(Auth::user()->hasRole('Teacher'))
                    <form action="{{ route('staff-attendance.check-in') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition shadow-sm">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            Check In
                        </button>
                    </form>
                    <form action="{{ route('staff-attendance.check-out') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white text-sm font-semibold rounded-xl hover:from-amber-700 hover:to-orange-700 transition shadow-sm">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i>
                            Check Out
                        </button>
                    </form>
                @endif
                @can('create', App\Models\StaffAttendance::class)
                <a href="{{ route('staff-attendance.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i>
                    New Record
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Staff Attendance" :data="$staffAttendances" searchable="true"
            searchPlaceholder="Search..." searchValue="{{ request('search') }}"
            searchRoute="{{ route('staff-attendance.index') }}">
            <x-slot name="filters">
                <div class="flex items-center gap-2 flex-wrap">
                    @if(Auth::user()->hasRole('Admin'))
                    <select name="teacher_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->user?->name ?? 'Teacher #'.$teacher->id }}
                        </option>
                        @endforeach
                    </select>
                    @endif
                    <select name="status" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                        <option value="on_leave" {{ request('status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="excused" {{ request('status') === 'excused' ? 'selected' : '' }}>Excused</option>
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 px-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teacher</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Check In</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($staffAttendances as $sa)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">
                        <div class="flex items-center gap-3">
                            <img src="{{ $sa->teacher?->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($sa->teacher?->user?->name ?? 'T') }}"
                                 alt="" class="w-8 h-8 rounded-full">
                            <div>
                                <p>{{ $sa->teacher?->user?->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $sa->teacher?->employee_number ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        {{ $sa->attendance_date?->format('M d, Y') ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $colors = ['present'=>'bg-emerald-100 text-emerald-700','absent'=>'bg-red-100 text-red-700','late'=>'bg-amber-100 text-amber-700','on_leave'=>'bg-blue-100 text-blue-700','excused'=>'bg-purple-100 text-purple-700'];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$sa->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst(str_replace('_', ' ', $sa->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        {{ $sa->check_in?->format('h:i A') ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                        {{ $sa->check_out?->format('h:i A') ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <a href="{{ route('staff-attendance.show', $sa) }}" title="View"
                           class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @can('update', $sa)
                        <a href="{{ route('staff-attendance.edit', $sa) }}" title="Edit"
                           class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endcan
                        @can('delete', $sa)
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('staff-attendance.destroy', $sa) }}',
                            method: 'DELETE',
                            title: 'Delete Record',
                            message: 'Delete this staff attendance record? This action cannot be undone.',
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
                    <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No staff attendance records found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>
</x-app-layout>
