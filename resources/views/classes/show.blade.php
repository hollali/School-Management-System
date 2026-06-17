<x-app-layout>
    @section('title', $class->name)

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('classes.index') }}" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-400 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ $class->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $class->grade_level ?? 'No grade' }} {{ $class->section ? '• ' . $class->section : '' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200">Students</h3>
                            @can('manage-classes')
                                <button @click="$dispatch('open-modal', 'assign-student')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-xs font-semibold rounded-lg hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                    <i class="fa-solid fa-plus"></i>
                                    Assign Student
                                </button>
                            @endcan
                        </div>
                        @if($class->students->count())
                            <div class="space-y-2">
                                @foreach($class->students as $student)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <img src="{{ $student->user?->profile_photo_url ?? '' }}" alt="" class="w-8 h-8 rounded-full shrink-0">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-slate-200 truncate">{{ $student->user?->name ?? 'Unknown' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-xs text-gray-400 dark:text-slate-500">{{ $student->admission_number ?? 'N/A' }}</span>
                                            @can('manage-classes')
                                                <button @click="$dispatch('set-confirmation', {
                                                    action: '{{ route('classes.students.remove', [$class, $student]) }}',
                                                    method: 'DELETE',
                                                    title: 'Remove Student',
                                                    message: 'Remove {{ $student->user?->name }} from {{ $class->name }}?',
                                                    confirmLabel: 'Remove',
                                                    confirmClass: 'bg-red-600 hover:bg-red-700'
                                                })" class="p-1 text-gray-400 hover:text-red-500 transition" title="Remove">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-6">No students assigned to this class.</p>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Subjects</h3>
                        @if($class->subjects->count())
                            <div class="flex flex-wrap gap-2">
                                @foreach($class->subjects as $subject)
                                    <span class="px-3 py-1.5 bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-xs font-semibold">{{ $subject->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 dark:text-slate-500">No subjects assigned.</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-3">Class Info</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-slate-400">Teacher</span>
                                <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $class->teacher?->user?->name ?? 'Unassigned' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-slate-400">Students</span>
                                <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $class->students->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-slate-400">Capacity</span>
                                <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $class->capacity ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-slate-400">Academic Year</span>
                                <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $class->academic_year ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @can('manage-classes')
                            <a href="{{ route('classes.index') }}" title="Edit"
                                class="inline-flex items-center justify-center w-10 h-10 bg-amber-50 text-amber-700 rounded-xl hover:bg-amber-100 transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endcan
                        @if(Auth::user()->hasRole('Teacher'))
                            <a href="{{ route('attendances.index') }}" title="Take Attendance"
                                class="inline-flex items-center justify-center w-10 h-10 bg-sky-50 text-sky-700 rounded-xl hover:bg-sky-100 transition">
                                <i class="fa-solid fa-check-to-slot"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="assign-student" maxWidth="lg" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Student to {{ $class->name }}</h2>
                <button @click="$dispatch('close-modal', 'assign-student')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-400 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('classes.students.assign', $class) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Select Student</label>
                        <select name="student_id" id="student_id" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Choose a student...</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->user?->name ?? 'Unknown' }} — {{ $student->admission_number ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 mt-8">
                    <button @click="$dispatch('close-modal', 'assign-student')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        Assign Student
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
