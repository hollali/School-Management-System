<x-app-layout>
    @section('title', 'Edit Staff Attendance')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Edit Staff Attendance</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Update staff attendance record</p>
            </div>
            <a href="{{ route('staff-attendance.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <form action="{{ route('staff-attendance.update', $staffAttendance) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Teacher</label>
                            <select name="teacher_id" required
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $staffAttendance->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user?->name ?? 'Teacher #'.$teacher->id }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                            <input type="date" name="attendance_date" value="{{ old('attendance_date', $staffAttendance->attendance_date->format('Y-m-d')) }}" required
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status</label>
                            <select name="status" required
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                @foreach(['present','absent','late','on_leave','excused'] as $status)
                                <option value="{{ $status }}" {{ old('status', $staffAttendance->status) === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Check In Time</label>
                                <input type="time" name="check_in" value="{{ old('check_in', $staffAttendance->check_in?->format('H:i')) }}"
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Check Out Time</label>
                                <input type="time" name="check_out" value="{{ old('check_out', $staffAttendance->check_out?->format('H:i')) }}"
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Remarks</label>
                            <textarea name="remarks" rows="3"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('remarks', $staffAttendance->remarks) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('staff-attendance.index') }}"
                               class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                <i class="fa-solid fa-check mr-2"></i>
                                Update Record
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
