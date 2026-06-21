@section('title', 'Edit Exam')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Edit Exam</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $exam->name }}</p>
            </div>
            <a href="{{ route('exams.show', $exam) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <form action="{{ route('exams.update', $exam) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam Title</label>
                        <input type="text" name="name" value="{{ old('name', $exam->name) }}" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam Type</label>
                        <select name="type" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select type</option>
                            @foreach(['Quiz', 'Midterm', 'Final', 'Practical', 'Continuous Assessment', 'Mock'] as $t)
                                <option value="{{ $t }}" @selected(old('type', $exam->type) == $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam Mode</label>
                        <select name="exam_mode" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="offline" @selected(old('exam_mode', $exam->exam_mode) == 'offline')>Offline / Record Only</option>
                            <option value="online" @selected(old('exam_mode', $exam->exam_mode) == 'online')>Online</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                        <select name="subject_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id', $exam->subject_id) == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                        <select name="class_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id', $exam->class_id) == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Term</label>
                        <select name="academic_term_id" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select term</option>
                            @foreach($academicTerms as $term)
                                <option value="{{ $term->id }}" @selected(old('academic_term_id', $exam->academic_term_id) == $term->id)>{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Term</label>
                        <input type="text" name="term" value="{{ old('term', $exam->term) }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Year</label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $exam->academic_year) }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Timing & Scoring</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" max="1440"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Total Marks</label>
                        <input type="number" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" step="0.01" min="0"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Pass Mark</label>
                        <input type="number" name="pass_mark" value="{{ old('pass_mark', $exam->pass_mark) }}" step="0.01" min="0"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Max Attempts</label>
                        <input type="number" name="max_attempts" value="{{ old('max_attempts', $exam->max_attempts) }}" min="1" max="100"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Start Date</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', $exam->start_date ? \Carbon\Carbon::parse($exam->start_date)->format('Y-m-d\TH:i') : '') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">End Date</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date', $exam->end_date ? \Carbon\Carbon::parse($exam->end_date)->format('Y-m-d\TH:i') : '') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Description & Instructions</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="3" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">{{ old('description', $exam->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Instructions</label>
                        <textarea name="instructions" rows="4" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">{{ old('instructions', $exam->instructions) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Security & Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php $settings = ['shuffle_questions', 'shuffle_options', 'negative_marking', 'fullscreen_required', 'tab_switch_detection', 'copy_paste_disabled', 'show_results_immediately']; @endphp
                    @foreach($settings as $field)
                        <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $exam->$field)) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm text-gray-700 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-4 max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Negative Mark Value</label>
                    <input type="number" name="negative_mark_value" value="{{ old('negative_mark_value', $exam->negative_mark_value) }}" step="0.01" min="0"
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('exams.show', $exam) }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
