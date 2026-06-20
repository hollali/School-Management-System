<x-app-layout>
    @section('title', 'Attendance Dashboard')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Attendance Dashboard</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                    @if($role === 'student') Your attendance overview
                    @elseif($role === 'teacher') Track your class attendance
                    @else School-wide attendance analytics
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('attendance.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-list mr-2"></i>
                    All Records
                </a>
                @can('mark', App\Models\Attendance::class)
                <a href="{{ route('attendance.mark') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-check-to-slot mr-2"></i>
                    Mark Attendance
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        @if($role === 'student')
            {{-- Student Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Attendance</p>
                    <p class="text-3xl font-bold {{ $summary['percentage'] >= 75 ? 'text-emerald-600' : 'text-red-600' }} mt-1">
                        {{ $summary['percentage'] }}%
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Present</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $summary['present'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">of {{ $summary['total'] }} school days</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Absent</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $summary['absent'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Late</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $summary['late'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Excused</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $summary['excused'] }}</p>
                </div>
            </div>

            @if($belowThreshold)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                <p class="text-sm text-red-700 dark:text-red-400">
                    Your attendance is below the required threshold of {{ \App\Services\AttendanceService::THRESHOLD_DEFAULT }}%.
                    Please contact your teacher.
                </p>
            </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Recent Attendance</h3>
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
                            @forelse($recentRecords as $record)
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
                                        $colors = ['present'=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400','absent'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400','late'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400','excused'=>'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$record->status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $record->remarks ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-12 text-gray-400">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($role === 'teacher')
            {{-- Teacher Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">My Classes</p>
                    <p class="text-3xl font-bold text-sky-600 mt-1">{{ $classes->count() }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Today's Attendance</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $todayAttendance->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">classes marked</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Missing Today</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $missingClasses->count() }}</p>
                </div>
            </div>

            @if($missingClasses->count() > 0)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-bell text-amber-500"></i>
                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">Attendance needed for:</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($missingClasses as $class)
                    <a href="{{ route('attendance.mark', ['class_id' => $class->id, 'attendance_date' => now()->format('Y-m-d')]) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-sm rounded-lg hover:bg-amber-200 dark:hover:bg-amber-900/50 transition">
                        <i class="fa-solid fa-arrow-right mr-1.5"></i>
                        {{ $class->name }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Class summaries --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Class Attendance Overview</h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($classSummaries as $cs)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $cs['class']->name }}</p>
                                @php $avg = round($cs['summary']->avg('percentage'), 1); @endphp
                                <span class="text-sm font-semibold {{ $avg >= 75 ? 'text-emerald-600' : 'text-red-600' }}">{{ $avg }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $avg >= 75 ? 'bg-emerald-500' : 'bg-red-500' }}" style="width: {{ $avg }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-gray-400">No data available.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Low attendance students --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Low Attendance Students</h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($lowAttendanceStudents as $s)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $s->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($s->user?->name ?? 'S') }}"
                                     alt="" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $s->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $s->admission_number ?? '' }}</p>
                                </div>
                            </div>
                            @php $pct = \App\Services\AttendanceService::THRESHOLD_DEFAULT; @endphp
                            <span class="text-sm font-semibold text-red-600">{{ $pct }}%</span>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-gray-400">All students have good attendance.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent attendance --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Recent Attendance Sessions</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($recentAttendance as $att)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $att->schoolClass?->name ?? 'Class' }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $att->attendance_date?->format('M d, Y') ?? '' }}</p>
                        </div>
                        <a href="{{ route('attendance.show', $att) }}" class="text-sm text-sky-600 hover:text-sky-700">View</a>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400">No recent attendance.</div>
                    @endforelse
                </div>
            </div>

        @elseif($role === 'admin')
            {{-- Admin Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Overall Rate</p>
                    <p class="text-3xl font-bold text-sky-600 mt-1">{{ $overallSummary['percentage'] }}%</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Present</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $overallSummary['present'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Absent</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $overallSummary['absent'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Late</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $overallSummary['late'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Excused</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $overallSummary['excused'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Class Performance</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $startDate }} to {{ $endDate }}</p>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($classPerformance as $cp)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $cp['class']->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $cp['total_students'] }} students</p>
                                </div>
                                <span class="text-sm font-semibold {{ $cp['avg_percentage'] >= 75 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $cp['avg_percentage'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $cp['avg_percentage'] >= 75 ? 'bg-emerald-500' : 'bg-red-500' }}" style="width: {{ $cp['avg_percentage'] }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-gray-400">No data available.</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Low Attendance Students</h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($lowAttendanceStudents->take(20) as $s)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $s->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($s->user?->name ?? 'S') }}"
                                     alt="" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $s->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $s->admission_number ?? '' }} &middot; {{ $s->classes->pluck('name')->implode(', ') }}</p>
                                </div>
                            </div>
                            @php
                                $svc = app(\App\Services\AttendanceService::class);
                                $sum = $svc->getStudentAttendanceSummary($s);
                            @endphp
                            <span class="text-sm font-semibold text-red-600">{{ $sum['percentage'] }}%</span>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-gray-400">All students have good attendance.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Today's Attendance</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($todayAttendance as $att)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $att->schoolClass?->name ?? 'Class' }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                {{ $att->records->where('status', 'present')->count() }} present,
                                {{ $att->records->where('status', 'absent')->count() }} absent,
                                {{ $att->records->where('status', 'late')->count() }} late
                            </p>
                        </div>
                        <a href="{{ route('attendance.show', $att) }}" class="text-sm text-sky-600 hover:text-sky-700">View</a>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">
                        <i class="fa-solid fa-calendar-xmark text-2xl mb-2"></i>
                        <p>No attendance recorded today.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
