<x-app-layout>
    @section('title', 'Mark Attendance')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Mark Attendance</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Record student attendance for a class</p>
            </div>
            <a href="{{ route('attendance.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Records
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-6">
            <form action="{{ route('attendance.mark') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                    <select name="class_id"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">Select class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ ($classId ?? '') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} {{ $class->section ? ' - '.$class->section : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($subjects->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject (optional)</label>
                    <select name="subject_id"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">No subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ ($subjectId ?? '') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                    <input type="date" name="attendance_date" value="{{ $date ?? now()->format('Y-m-d') }}"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-magnifying-glass mr-2"></i>
                        Load Students
                    </button>
                </div>
            </form>
        </div>

        @if(isset($students) && $students->count() > 0)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">
                        {{ $attendance?->schoolClass?->name ?? 'Class' }}
                        <span class="text-sm font-normal text-gray-500 dark:text-slate-400 ml-2">
                            {{ $date ? \Carbon\Carbon::parse($date)->format('l, F d, Y') : '' }}
                        </span>
                    </h3>
                    @php
                        $isWeekend = $date && \App\Services\AttendanceService::isWeekend($date);
                        $isHoliday = $date && \App\Services\AttendanceService::isHoliday($date);
                    @endphp
                    @if($isWeekend)
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        This date falls on a weekend — it is excluded from attendance summaries.
                    </p>
                    @endif
                    @if($isHoliday)
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">
                        <i class="fa-solid fa-calendar-xmark mr-1"></i>
                        This date is a holiday — it is excluded from attendance summaries.
                    </p>
                    @endif
                    @if($isExisting)
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                        <i class="fa-solid fa-pen-to-square mr-1"></i>
                        Editing existing attendance record
                    </p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-slate-400">
                        {{ $students->count() }} students
                    </span>
                    <button type="button" onclick="setAllStatus('present')"
                        class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-semibold rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition">
                        All Present
                    </button>
                    <button type="button" onclick="setAllStatus('absent')"
                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-xs font-semibold rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                        All Absent
                    </button>
                </div>
            </div>

            <form action="{{ route('attendance.mark.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classId }}">
                <input type="hidden" name="attendance_date" value="{{ $date }}">
                @if($subjectId)<input type="hidden" name="subject_id" value="{{ $subjectId }}">@endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                        <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Present</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Absent</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Late</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Excused</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($students as $index => $record)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $record->student?->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($record->student?->user?->name ?? 'Student') }}"
                                             alt="" class="w-8 h-8 rounded-full ring-2 ring-gray-200 dark:ring-slate-600">
                                        <div>
                                            <p class="font-medium">{{ $record->student?->user?->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $record->student?->admission_number ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                @foreach(['present', 'absent', 'late', 'excused'] as $status)
                                <td class="px-6 py-3 text-center">
                                    <input type="radio"
                                           name="students[{{ $record->student_id }}][status]"
                                           value="{{ $status }}"
                                           {{ $record->status === $status ? 'checked' : '' }}
                                           class="w-4 h-4 text-sky-600 border-gray-300 dark:border-slate-600 focus:ring-sky-500
                                                  {{ $status === 'present' ? 'text-emerald-600' : '' }}
                                                  {{ $status === 'absent' ? 'text-red-600' : '' }}
                                                  {{ $status === 'late' ? 'text-amber-600' : '' }}
                                                  {{ $status === 'excused' ? 'text-purple-600' : '' }}">
                                </td>
                                @endforeach
                                <td class="px-6 py-3">
                                    <input type="text"
                                           name="students[{{ $record->student_id }}][remarks]"
                                           value="{{ $record->remarks }}"
                                           placeholder="Optional note..."
                                           class="block w-full rounded-lg border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-xs py-1.5 px-3">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Session Notes</label>
                        <textarea name="notes" rows="2" class="block w-full max-w-md rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2 px-4">{{ $attendance->notes ?? '' }}</textarea>
                    </div>
                    <div class="flex items-center gap-3 ml-4">
                        <a href="{{ route('attendance.index') }}"
                           class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            <i class="fa-solid fa-check mr-2"></i>
                            {{ $isExisting ? 'Update Attendance' : 'Save Attendance' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @elseif(request()->has('class_id') && request()->has('attendance_date'))
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-12 text-center">
            <i class="fa-solid fa-users-slash text-4xl text-gray-300 dark:text-slate-600 mb-4"></i>
            <p class="text-gray-500 dark:text-slate-400">No students found in this class.</p>
        </div>
        @else
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-12 text-center">
            <i class="fa-solid fa-hand-pointer text-4xl text-gray-300 dark:text-slate-600 mb-4"></i>
            <p class="text-gray-500 dark:text-slate-400">Select a class and date above to start marking attendance.</p>
        </div>
        @endif
    </div>

    <script>
    function setAllStatus(status) {
        document.querySelectorAll('input[type="radio"][value="' + status + '"]').forEach(el => el.checked = true);
    }
    </script>
</x-app-layout>
