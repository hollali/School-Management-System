<x-app-layout>
    @section('title', 'Attendance')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Attendance</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Track and manage student attendance records</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Attendance" :data="$attendances" searchable="true" searchPlaceholder="Search attendance records..." searchValue="{{ request('search') }}" searchRoute="{{ route('attendances.index') }}">
            @if(Auth::user()->hasRole('Teacher'))
            <x-slot name="actions">
                <button @click="$dispatch('open-modal', 'create-attendance')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Attendance
                </button>
            </x-slot>
            @endif

            <x-slot name="filters">
                <form action="{{ route('attendances.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="class_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}{{ $class->section ? ' - '.$class->section : '' }}</option>
                        @endforeach
                    </select>
                    @foreach (request()->except('class_id', 'page') as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}" />
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                        @endif
                    @endforeach
                </form>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Class</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Time</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Teacher</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($attendances as $attendance)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $attendance->schoolClass->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $attendance->attendance_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $attendance->attendance_date->format('h:i A') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($attendance->status ?? 'present') === 'present' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($attendance->status ?? 'present') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $attendance->creator->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <button @click="
                            $dispatch('view-attendance', @js([
                                'class_name' => $attendance->schoolClass->name,
                                'attendance_date' => $attendance->attendance_date->format('l, F d, Y'),
                                'creator_name' => $attendance->creator->name,
                                'notes' => $attendance->notes,
                            ]));
                            $dispatch('open-modal', 'view-attendance');
                        " title="View" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        @if(Auth::user()->hasRole('Teacher'))
                        <button @click="
                            $dispatch('edit-attendance', @js([
                                'id' => $attendance->id,
                                'class_id' => $attendance->class_id,
                                'attendance_date' => $attendance->attendance_date->format('Y-m-d'),
                                'notes' => $attendance->notes,
                            ]));
                            $dispatch('open-modal', 'edit-attendance');
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('attendances.destroy', $attendance) }}',
                            method: 'DELETE',
                            title: 'Delete Attendance',
                            message: 'Delete this attendance record? This action cannot be undone.',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })" title="Delete" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                        @endif
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

    <x-modal name="create-attendance" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Attendance') }}</h2>
                <button @click="$dispatch('close-modal', 'create-attendance')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('attendances.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                        <select name="class_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white"
                            required>
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} {{ $class->section ? ' - '.$class->section : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                        <input type="date" name="attendance_date" value="{{ old('attendance_date') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"
                            required>
                        @error('attendance_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Notes</label>
                        <textarea name="notes" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'create-attendance')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            Record Attendance
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-attendance" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editAttendanceData()" @edit-attendance.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Attendance</h2>
                <button @click="$dispatch('close-modal', 'edit-attendance')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/attendances/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                        <select name="class_id" x-model="form.class_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white"
                            required>
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} {{ $class->section ? ' - '.$class->section : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                        <input type="date" name="attendance_date" x-model="form.attendance_date"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Notes</label>
                        <textarea name="notes" x-model="form.notes" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'edit-attendance')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            Save changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-attendance" maxWidth="2xl" focusable>
        <div class="p-6" x-data="{ data: null }" @view-attendance.window="data = $event.detail; $dispatch('open-modal', 'view-attendance')">
            <template x-if="data">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Attendance Details</h2>
                        <button @click="$dispatch('close-modal', 'view-attendance')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Class</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.class_name"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Date</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.attendance_date"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Recorded By</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.creator_name"></p>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                            <p class="text-sm font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Notes</p>
                            <p class="text-gray-900 dark:text-slate-200" x-text="data.notes || '—'"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </x-modal>

    <script>
        function editAttendanceData() {
            return {
                form: { id: '', class_id: '', attendance_date: '', notes: '' },
                load(data) {
                    this.form = { ...data };
                    this.$dispatch('open-modal', 'edit-attendance');
                }
            };
        }
    </script>
</x-app-layout>
