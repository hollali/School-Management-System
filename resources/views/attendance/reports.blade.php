<x-app-layout>
    @section('title', 'Attendance Reports')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Attendance Reports</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Generate and view attendance reports</p>
            </div>
            <a href="{{ route('attendance.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-chart-simple mr-2"></i>
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <form action="{{ route('attendance.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Report Type</label>
                    <select name="report_type" onchange="toggleReportFields(this.value)"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="class" {{ ($reportType ?? 'class') === 'class' ? 'selected' : '' }}>Class Report</option>
                        <option value="student" {{ ($reportType ?? '') === 'student' ? 'selected' : '' }}>Student Report</option>
                        @if(Auth::user()->hasRole('Admin'))
                        <option value="subject" {{ ($reportType ?? '') === 'subject' ? 'selected' : '' }}>Subject Report</option>
                        <option value="teacher" {{ ($reportType ?? '') === 'teacher' ? 'selected' : '' }}>Teacher Report</option>
                        @endif
                        <option value="date_range" {{ ($reportType ?? '') === 'date_range' ? 'selected' : '' }}>Date Range Summary</option>
                    </select>
                </div>

                <div id="class-field" class="report-field">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                    <select name="class_id"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">Select class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="student-field" class="report-field" style="display:none">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                    <select name="student_id"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">Select student</option>
                        @foreach(\App\Models\Student::with('user')->get() as $s)
                        <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->user?->name ?? 'Unknown' }} ({{ $s->admission_number ?? '' }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="subject-field" class="report-field" style="display:none">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                    <select name="subject_id"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">Select subject</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="teacher-field" class="report-field" style="display:none">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Teacher</label>
                    <select name="teacher_id"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">Select teacher</option>
                        @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->user?->name ?? 'Unknown' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>

                <div class="flex items-end">
                    <button type="submit" name="generate" value="1"
                        class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-chart-bar mr-2"></i>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        @if($reportData)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">
                    @switch($reportData['type'])
                        @case('student') Student Report: {{ $reportData['student']->user?->name ?? '' }} @break
                        @case('class') Class Report: {{ $reportData['class']->name ?? '' }} @break
                        @case('subject') Subject Report: {{ $reportData['subject']->name ?? '' }} @break
                        @case('teacher') Teacher Report: {{ $reportData['teacher']->user?->name ?? '' }} @break
                        @case('date_range') Date Range Report: {{ $reportData['date_from'] }} to {{ $reportData['date_to'] }} @break
                    @endswitch
                </h3>
            </div>

            <div class="p-6">
                @if($reportData['type'] === 'student')
                <div class="grid grid-cols-5 gap-4 mb-6">
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-emerald-600">{{ $reportData['summary']['present'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Present</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-red-600">{{ $reportData['summary']['absent'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Absent</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-amber-600">{{ $reportData['summary']['late'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Late</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ $reportData['summary']['excused'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Excused</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-sky-600">{{ $reportData['summary']['percentage'] }}%</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Rate</p>
                    </div>
                </div>
                <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                    <thead><tr><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Class</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Subject</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Remarks</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($reportData['records'] as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 text-sm">{{ $record->attendance?->attendance_date?->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $record->attendance?->schoolClass?->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $record->attendance?->subject?->name ?? '—' }}</td>
                            <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ match($record->status) {'present'=>'bg-emerald-100 text-emerald-700','absent'=>'bg-red-100 text-red-700','late'=>'bg-amber-100 text-amber-700','excused'=>'bg-purple-100 text-purple-700',default=>'bg-gray-100'} }}">{{ ucfirst($record->status) }}</span></td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $record->remarks ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @elseif($reportData['type'] === 'class')
                <div class="mb-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Class Average: <strong class="text-gray-900 dark:text-slate-200">{{ $reportData['avg_percentage'] }}%</strong></p>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Attendance Sessions: {{ $reportData['attendance_sessions']->count() }}</p>
                </div>
                <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                    <thead><tr><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Total</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Present</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Absent</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Late</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Excused</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">%</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($reportData['summaries'] as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 text-sm font-medium">{{ $s['student']->user?->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3 text-sm text-center">{{ $s['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-emerald-600">{{ $s['present'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-red-600">{{ $s['absent'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-amber-600">{{ $s['late'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-purple-600">{{ $s['excused'] }}</td>
                            <td class="px-4 py-3 text-sm text-center font-semibold {{ $s['percentage'] >= 75 ? 'text-emerald-600' : 'text-red-600' }}">{{ $s['percentage'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @elseif($reportData['type'] === 'subject')
                <div class="grid grid-cols-5 gap-4 mb-4">
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $reportData['present'] }}</p><p class="text-xs text-gray-500">Present</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-red-600">{{ $reportData['absent'] }}</p><p class="text-xs text-gray-500">Absent</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-amber-600">{{ $reportData['late'] }}</p><p class="text-xs text-gray-500">Late</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-purple-600">{{ $reportData['excused'] }}</p><p class="text-xs text-gray-500">Excused</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-sky-600">{{ $reportData['percentage'] }}%</p><p class="text-xs text-gray-500">Rate</p></div>
                </div>

                @elseif($reportData['type'] === 'teacher')
                <div class="mb-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Attendance Sessions: <strong>{{ $reportData['session_count'] }}</strong></p>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Total Records: <strong>{{ $reportData['total_records'] }}</strong></p>
                </div>

                @elseif($reportData['type'] === 'date_range')
                <div class="grid grid-cols-5 gap-4 mb-6">
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $reportData['summary']['present'] }}</p><p class="text-xs text-gray-500">Present</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-red-600">{{ $reportData['summary']['absent'] }}</p><p class="text-xs text-gray-500">Absent</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-amber-600">{{ $reportData['summary']['late'] }}</p><p class="text-xs text-gray-500">Late</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-purple-600">{{ $reportData['summary']['excused'] }}</p><p class="text-xs text-gray-500">Excused</p></div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-sky-600">{{ $reportData['summary']['percentage'] }}%</p><p class="text-xs text-gray-500">Rate</p></div>
                </div>

                <h4 class="text-md font-semibold text-gray-900 dark:text-slate-200 mb-3">Daily Breakdown</h4>
                <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                    <thead><tr><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Total</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Present</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Absent</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Late</th><th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Excused</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($reportData['daily_breakdown'] as $day)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 text-sm font-medium">{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-center">{{ $day['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-emerald-600">{{ $day['present'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-red-600">{{ $day['absent'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-amber-600">{{ $day['late'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-purple-600">{{ $day['excused'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-12 text-center">
            <i class="fa-solid fa-chart-pie text-4xl text-gray-300 dark:text-slate-600 mb-4"></i>
            <p class="text-gray-500 dark:text-slate-400">Select report parameters and click "Generate Report" to view data.</p>
        </div>
        @endif
    </div>

    <script>
    function toggleReportFields(type) {
        document.querySelectorAll('.report-field').forEach(el => el.style.display = 'none');
        const fieldMap = { class: 'class-field', student: 'student-field', subject: 'subject-field', teacher: 'teacher-field' };
        const el = document.getElementById(fieldMap[type]);
        if (el) el.style.display = 'block';
    }
    toggleReportFields('{{ $reportType ?? "class" }}');
    </script>
</x-app-layout>
