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
                        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Students</h3>
                        @if($class->students->count())
                            <div class="space-y-2">
                                @foreach($class->students as $student)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $student->user?->profile_photo_url ?? '' }}" alt="" class="w-8 h-8 rounded-full">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $student->user?->name ?? 'Unknown' }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400 dark:text-slate-500">{{ $student->admission_number ?? 'N/A' }}</span>
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
</x-app-layout>
