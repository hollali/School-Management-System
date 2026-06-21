@section('title', 'Create Exam')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Create Exam</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Set up a new examination</p>
            </div>
            <a href="{{ route('exams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <form action="{{ route('exams.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam Title *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                        @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam Type</label>
                        <select name="type" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select type</option>
                            <option value="Quiz" @selected(old('type') == 'Quiz')>Quiz</option>
                            <option value="Midterm" @selected(old('type') == 'Midterm')>Midterm</option>
                            <option value="Final" @selected(old('type') == 'Final')>Final Exam</option>
                            <option value="Practical" @selected(old('type') == 'Practical')>Practical</option>
                            <option value="Continuous Assessment" @selected(old('type') == 'Continuous Assessment')>Continuous Assessment</option>
                            <option value="Mock" @selected(old('type') == 'Mock')>Mock Exam</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam Mode</label>
                        <select name="exam_mode" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="offline" @selected(old('exam_mode') == 'offline')>Offline / Record Only</option>
                            <option value="online" @selected(old('exam_mode') == 'online')>Online</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                        <select name="subject_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                        <select name="class_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Term</label>
                        <select name="academic_term_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select term</option>
                            @foreach($academicTerms as $term)
                                <option value="{{ $term->id }}" @selected(old('academic_term_id') == $term->id)>{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Term (text)</label>
                        <input type="text" name="term" value="{{ old('term') }}" placeholder="e.g., First Semester"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Year</label>
                        <input type="text" name="academic_year" value="{{ old('academic_year') }}" placeholder="e.g., 2025-2026"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Timing & Scoring</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" max="1440"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Total Marks</label>
                        <input type="number" name="total_marks" value="{{ old('total_marks') }}" step="0.01" min="0"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Pass Mark</label>
                        <input type="number" name="pass_mark" value="{{ old('pass_mark') }}" step="0.01" min="0"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Max Attempts</label>
                        <input type="number" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" max="100"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">End Date & Time</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Description & Instructions</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="3" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Instructions for Students</label>
                        <textarea name="instructions" rows="4" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700" placeholder="Enter exam instructions, rules, and guidelines...">{{ old('instructions') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Security & Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <input type="checkbox" name="shuffle_questions" value="1" @checked(old('shuffle_questions')) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Shuffle Questions</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <input type="checkbox" name="shuffle_options" value="1" @checked(old('shuffle_options')) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Shuffle Options</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <input type="checkbox" name="negative_marking" value="1" @checked(old('negative_marking')) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Negative Marking</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <input type="checkbox" name="fullscreen_required" value="1" @checked(old('fullscreen_required')) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Require Fullscreen</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <input type="checkbox" name="tab_switch_detection" value="1" @checked(old('tab_switch_detection')) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Detect Tab Switches</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <input type="checkbox" name="copy_paste_disabled" value="1" @checked(old('copy_paste_disabled')) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Disable Copy/Paste</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <input type="checkbox" name="show_results_immediately" value="1" @checked(old('show_results_immediately')) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Show Results Immediately</span>
                    </label>
                </div>
                <div class="mt-4 max-w-xs" x-data="{ showNeg: $el.querySelector('input[name=negative_marking]').checked }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Negative Mark Value</label>
                    <input type="number" name="negative_mark_value" value="{{ old('negative_mark_value', 0) }}" step="0.01" min="0"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('exams.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Create Exam
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
