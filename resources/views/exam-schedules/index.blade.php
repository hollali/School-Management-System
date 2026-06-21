@section('title', 'Exam Schedules')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Exam Schedules</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage exam timetables and rooms</p>
            </div>
            @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('Admin'))
                <button @click="$dispatch('open-modal', 'create-schedule')" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus"></i> New Schedule
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Timetable" :data="$schedules">
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Exam</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Room</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $schedule->exam->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $schedule->class?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $schedule->exam_date?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $schedule->room?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-right space-x-1">
                            @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('Admin'))
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('exam-schedules.destroy', $schedule) }}',
                                    method: 'DELETE',
                                    title: 'Remove Schedule',
                                    message: 'Remove this schedule?',
                                    confirmLabel: 'Remove',
                                    confirmClass: 'bg-red-600 hover:bg-red-700'
                                })" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No schedules found.</td></tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-schedule" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">New Schedule</h2>
                <button @click="$dispatch('close-modal', 'create-schedule')" type="button" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('exam-schedules.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam</label>
                        <select name="exam_id" required class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->name }} ({{ $exam->type ?? '—' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                        <select name="class_id" required class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Room</label>
                        <select name="room_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">No room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->capacity }} seats)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                        <input type="date" name="exam_date" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Start Time</label>
                        <input type="time" name="start_time" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">End Time</label>
                        <input type="time" name="end_time" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Notes</label>
                        <textarea name="notes" rows="2" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="$dispatch('close-modal', 'create-schedule')" type="button" class="px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Create Schedule</button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
