@section('title', 'Edit Question')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Edit Question</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $question->subject?->name }}</p>
            </div>
            <a href="{{ route('questions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <form action="{{ route('questions.update', $question) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                        <select name="subject_id" required class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id', $question->subject_id) == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Question Type</label>
                        <select name="question_type" id="question-type" onchange="toggleOptions(this.value)"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            @foreach(['mcq' => 'Multiple Choice', 'true_false' => 'True/False', 'fill_blank' => 'Fill in the Blank', 'short_answer' => 'Short Answer', 'essay' => 'Essay', 'multi_select' => 'Multi-Select', 'numeric' => 'Numeric Answer'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('question_type', $question->question_type) == $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Difficulty</label>
                        <select name="difficulty" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            @foreach(['easy', 'medium', 'hard'] as $d)
                                <option value="{{ $d }}" @selected(old('difficulty', $question->difficulty) == $d)>{{ ucfirst($d) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Default Marks</label>
                        <input type="number" name="default_marks" value="{{ old('default_marks', $question->default_marks) }}" step="0.01" min="0"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Topic</label>
                        <input type="text" name="topic" value="{{ old('topic', $question->topic) }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tags (comma separated)</label>
                        <input type="text" name="tags" value="{{ old('tags', $question->tags ? implode(', ', $question->tags) : '') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Question Text</label>
                        <textarea name="question_text" rows="3" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">{{ old('question_text', $question->question_text) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Explanation</label>
                        <textarea name="explanation" rows="2"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">{{ old('explanation', $question->explanation) }}</textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $question->is_active)) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm text-gray-700 dark:text-slate-300">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            <div id="options-section" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-slate-200">Answer Options</h3>
                    <button type="button" onclick="addOption()" class="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-600 text-white text-xs font-semibold rounded-lg hover:bg-sky-700 transition">
                        <i class="fa-solid fa-plus"></i> Add Option
                    </button>
                </div>
                <div id="options-container" class="space-y-3">
                    @foreach($question->options as $idx => $option)
                        <div class="option-row flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <input type="text" name="options[{{ $idx }}][option_text]" value="{{ $option->option_text }}" required
                                class="flex-1 rounded-lg border-gray-200 dark:border-slate-600 text-sm py-2 px-3 dark:bg-slate-700 dark:text-slate-200">
                            <label class="flex items-center gap-2 shrink-0 cursor-pointer">
                                <input type="checkbox" name="options[{{ $idx }}][is_correct]" value="1" @checked($option->is_correct) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs text-gray-600 dark:text-slate-400">Correct</span>
                            </label>
                            <input type="hidden" name="options[{{ $idx }}][id]" value="{{ $option->id }}">
                            <input type="hidden" name="options[{{ $idx }}][order]" value="{{ $option->order ?? $idx + 1 }}">
                            <button type="button" onclick="this.closest('.option-row').remove()" class="text-red-400 hover:text-red-600 shrink-0 p-1">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('questions.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-save mr-2"></i> Update Question
                </button>
            </div>
        </form>
    </div>

    <script>
        let optionIndex = {{ $question->options->count() }};
        function addOption() {
            const container = document.getElementById('options-container');
            const div = document.createElement('div');
            div.className = 'option-row flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg';
            div.innerHTML = `
                <input type="text" name="options[${optionIndex}][option_text]" placeholder="Option text" required
                    class="flex-1 rounded-lg border-gray-200 dark:border-slate-600 text-sm py-2 px-3 dark:bg-slate-700 dark:text-slate-200">
                <label class="flex items-center gap-2 shrink-0 cursor-pointer">
                    <input type="checkbox" name="options[${optionIndex}][is_correct]" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs text-gray-600 dark:text-slate-400">Correct</span>
                </label>
                <input type="hidden" name="options[${optionIndex}][order]" value="${optionIndex + 1}">
                <button type="button" onclick="this.closest('.option-row').remove()" class="text-red-400 hover:text-red-600 shrink-0 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            container.appendChild(div);
            optionIndex++;
        }
    </script>
</x-app-layout>
