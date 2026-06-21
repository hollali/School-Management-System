@section('title', 'Grade Submission')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Grade: {{ $attempt->student->user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $attempt->exam->name }} • {{ $attempt->exam->subject?->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('grading.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                <form action="{{ route('grading.finalize', $attempt) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm"
                        onclick="return confirm('Finalize grading? This will compute the final score and cannot be undone.')">
                        <i class="fa-solid fa-check"></i> Finalize
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <form id="grading-form" class="space-y-4">
            @csrf
            <div class="space-y-4">
                @foreach($attempt->exam->questions as $index => $question)
                    @php $answer = $attempt->answers->firstWhere('question_id', $question->id); @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-slate-200">Q{{ $index + 1 }}. {{ $question->question_text }}</p>
                                <span class="text-xs text-gray-500 dark:text-slate-400">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }} • {{ $question->pivot->marks ?? $question->default_marks }} marks</span>
                            </div>
                            <div class="text-right shrink-0 ml-4">
                                <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Marks</label>
                                <input type="number" name="answers[{{ $answer?->id }}][marks]" value="{{ $answer?->marks_obtained ?? 0 }}"
                                    step="0.01" min="0" max="{{ $question->pivot->marks ?? $question->default_marks }}"
                                    class="w-24 rounded-lg border-gray-200 dark:border-slate-600 text-sm py-1.5 px-2 text-center dark:bg-slate-700 dark:text-slate-200">
                            </div>
                        </div>

                        {{-- Student Answer --}}
                        @if($answer)
                            @if($answer->selectedOption)
                                <div class="p-3 rounded-lg mb-3 @if($answer->selectedOption->is_correct) bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 @else bg-gray-50 dark:bg-slate-700/50 @endif">
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">Student's Answer</p>
                                    <p class="text-sm text-gray-900 dark:text-slate-200">{{ $answer->selectedOption->option_text }}</p>
                                    @if($answer->selectedOption->is_correct)
                                        <p class="text-xs text-emerald-600 mt-1"><i class="fa-solid fa-check"></i> Correct</p>
                                    @endif
                                </div>
                            @endif
                            @if($answer->answer_text)
                                <div class="p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg mb-3">
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">Student's Answer</p>
                                    <p class="text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap">{{ $answer->answer_text }}</p>
                                </div>
                            @endif
                            @if($question->options->count() > 0 && !$question->question_type === 'essay')
                                <details class="text-sm">
                                    <summary class="cursor-pointer text-sky-600 hover:text-sky-800">Show correct answer</summary>
                                    <div class="mt-2 space-y-1">
                                        @foreach($question->options as $opt)
                                            <p class="text-sm @if($opt->is_correct) text-emerald-600 font-semibold @else text-gray-500 @endif">
                                                @if($opt->is_correct) <i class="fa-solid fa-check mr-1"></i> @endif
                                                {{ $opt->option_text }}
                                            </p>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        @else
                            <div class="p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <p class="text-sm text-gray-400 dark:text-slate-500 italic">Student did not answer this question.</p>
                            </div>
                        @endif

                        <div class="mt-3">
                            <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">Feedback</label>
                            <textarea name="answers[{{ $answer?->id }}][feedback]" rows="2"
                                class="block w-full rounded-lg border-gray-200 dark:border-slate-600 text-sm py-2 px-3 dark:bg-slate-700 dark:text-slate-200">{{ $answer?->feedback }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="saveGrades()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-600 text-white text-sm font-semibold rounded-xl hover:bg-sky-700 transition shadow-sm">
                    <i class="fa-solid fa-save"></i> Save Grades
                </button>
            </div>
        </form>
    </div>

    <script>
        async function saveGrades() {
            const form = document.getElementById('grading-form');
            const formData = new FormData(form);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route('grading.save', $attempt) }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: 'Grades saved!' } }));
                }
            } catch(e) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: 'Failed to save grades.' } }));
            }
        }
    </script>
</x-app-layout>
