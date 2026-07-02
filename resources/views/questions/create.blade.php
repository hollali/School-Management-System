@section('title', 'Create Question')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Create Question</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Add to reusable question bank</p>
            </div>
            <a href="{{ route('questions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject *</label>
                        <select name="subject_id" required class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Question Type *</label>
                        <select name="question_type" id="question-type" required onchange="toggleOptions(this.value)"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select type</option>
                            @foreach(['mcq' => 'Multiple Choice', 'true_false' => 'True/False', 'fill_blank' => 'Fill in the Blank', 'short_answer' => 'Short Answer', 'essay' => 'Essay', 'multi_select' => 'Multi-Select', 'numeric' => 'Numeric Answer'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('question_type') == $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Difficulty</label>
                        <select name="difficulty" class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                            <option value="easy" @selected(old('difficulty') == 'easy')>Easy</option>
                            <option value="medium" @selected(old('difficulty') == 'medium') selected>Medium</option>
                            <option value="hard" @selected(old('difficulty') == 'hard')>Hard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Default Marks</label>
                        <input type="number" name="default_marks" value="{{ old('default_marks', 1) }}" step="0.01" min="0"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Topic / Chapter</label>
                        <input type="text" name="topic" value="{{ old('topic') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tags (comma separated)</label>
                        <input type="text" name="tags" value="{{ old('tags') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Question Text *</label>
                        <textarea name="question_text" rows="3" required
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">{{ old('question_text') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Explanation (shown after answering)</label>
                        <textarea name="explanation" rows="2"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">{{ old('explanation') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Attachment (image, document, etc.)</label>
                        <div x-data="{ fileName: '' }" class="mt-1 flex items-center gap-3">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                <i class="fa-solid fa-upload mr-2"></i> Choose file
                                <input type="file" name="image" accept="image/*,.pdf,.doc,.docx" class="hidden" @change="fileName = $el.files[0]?.name || ''">
                            </label>
                            <span class="text-xs text-gray-400 dark:text-slate-500" x-show="!fileName">Max 2MB</span>
                            <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium truncate max-w-[200px]" x-show="fileName" x-text="fileName"></span>
                        </div>
                        @error('image')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm text-gray-700 dark:text-slate-300">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Options --}}
            <div id="options-section" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6" style="display:none">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-slate-200">Answer Options</h3>
                    <button type="button" onclick="addOption()" class="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-600 text-white text-xs font-semibold rounded-lg hover:bg-sky-700 transition">
                        <i class="fa-solid fa-plus"></i> Add Option
                    </button>
                </div>
                <div id="options-container" class="space-y-3">
                    <div class="option-row flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <input type="text" name="options[0][option_text]" placeholder="Option text" required
                            class="flex-1 rounded-lg border-gray-200 dark:border-slate-600 text-sm py-2 px-3 dark:bg-slate-700 dark:text-slate-200">
                        <label class="flex items-center gap-2 shrink-0 cursor-pointer">
                            <input type="checkbox" name="options[0][is_correct]" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs text-gray-600 dark:text-slate-400">Correct</span>
                        </label>
                        <input type="hidden" name="options[0][order]" value="1">
                        <button type="button" onclick="this.closest('.option-row').remove()" class="text-red-400 hover:text-red-600 shrink-0 p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="option-row flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <input type="text" name="options[1][option_text]" placeholder="Option text" required
                            class="flex-1 rounded-lg border-gray-200 dark:border-slate-600 text-sm py-2 px-3 dark:bg-slate-700 dark:text-slate-200">
                        <label class="flex items-center gap-2 shrink-0 cursor-pointer">
                            <input type="checkbox" name="options[1][is_correct]" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs text-gray-600 dark:text-slate-400">Correct</span>
                        </label>
                        <input type="hidden" name="options[1][order]" value="2">
                        <button type="button" onclick="this.closest('.option-row').remove()" class="text-red-400 hover:text-red-600 shrink-0 p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('questions.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-save mr-2"></i> Create Question
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleOptions(type) {
            const section = document.getElementById('options-section');
            const needsOptions = ['mcq', 'true_false', 'multi_select'];
            if (needsOptions.includes(type)) {
                section.style.display = 'block';
                if (type === 'true_false') {
                    const container = document.getElementById('options-container');
                    container.innerHTML = `
                        <div class="option-row flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <input type="text" name="options[0][option_text]" value="True" readonly
                                class="flex-1 rounded-lg border-gray-200 dark:border-slate-600 text-sm py-2 px-3 dark:bg-slate-700 dark:text-slate-200 bg-gray-100">
                            <label class="flex items-center gap-2 shrink-0 cursor-pointer">
                                <input type="radio" name="correct_tf" value="0" class="border-gray-300 text-emerald-600 focus:ring-emerald-500" checked>
                                <span class="text-xs text-gray-600 dark:text-slate-400">Correct</span>
                            </label>
                            <input type="hidden" name="options[0][is_correct]" value="1">
                            <input type="hidden" name="options[0][order]" value="1">
                        </div>
                        <div class="option-row flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <input type="text" name="options[1][option_text]" value="False" readonly
                                class="flex-1 rounded-lg border-gray-200 dark:border-slate-600 text-sm py-2 px-3 dark:bg-slate-700 dark:text-slate-200 bg-gray-100">
                            <label class="flex items-center gap-2 shrink-0 cursor-pointer">
                                <input type="radio" name="correct_tf" value="1" class="border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs text-gray-600 dark:text-slate-400">Correct</span>
                            </label>
                            <input type="hidden" name="options[1][is_correct]" value="0">
                            <input type="hidden" name="options[1][order]" value="2">
                        </div>
                    `;
                    // Handle TF radio toggle
                    document.querySelectorAll('input[name="correct_tf"]').forEach(r => {
                        r.addEventListener('change', function() {
                            document.querySelectorAll('input[name="options[0][is_correct]"], input[name="options[1][is_correct]"]').forEach(h => h.value = '0');
                            const idx = this.value;
                            document.querySelector(`input[name="options[${idx}][is_correct]"]`).value = '1';
                        });
                    });
                } else {
                    const container = document.getElementById('options-container');
                    const existing = container.querySelectorAll('.option-row');
                    if (existing.length === 0 || (existing.length === 2 && existing[0].querySelector('input[readonly]'))) {
                        container.innerHTML = '';
                        for (let i = 0; i < 2; i++) addOption();
                    }
                }
            } else {
                section.style.display = 'none';
            }
        }

        let optionIndex = 2;
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

        // Initialize on load if editing
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('question-type');
            if (typeSelect.value) toggleOptions(typeSelect.value);
        });
    </script>
</x-app-layout>
