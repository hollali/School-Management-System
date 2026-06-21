@section('title', 'Exam Result')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Exam Result</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $attempt->exam->name }}</p>
            </div>
            <a href="{{ route('student.exams') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Score Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="text-center">
                @if($attempt->status === 'graded')
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-4
                        @if($attempt->result_status === 'pass') bg-emerald-100 dark:bg-emerald-900/30 @else bg-red-100 dark:bg-red-900/30 @endif">
                        <i class="fa-solid @if($attempt->result_status === 'pass') fa-check-circle text-emerald-500 @else fa-xmark-circle text-red-500 @endif text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold @if($attempt->result_status === 'pass') text-emerald-600 @else text-red-600 @endif mb-1">
                        {{ $attempt->result_status === 'pass' ? 'PASSED' : 'FAILED' }}
                    </h3>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $attempt->percentage_score }}%</p>
                    <p class="text-sm text-gray-500 dark:text-slate-400">
                        Score: {{ $attempt->total_score }} / {{ $attempt->exam->questions->sum('pivot.marks') ?? $attempt->exam->total_marks ?? 0 }}
                    </p>
                @else
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-amber-100 dark:bg-amber-900/30 mb-4">
                        <i class="fa-solid fa-clock text-amber-500 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-amber-600 mb-1">Awaiting Results</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Your exam has been submitted and is pending review.</p>
                @endif
            </div>
        </div>

        @if($attempt->status === 'graded' && $attempt->exam->results_published)
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Question Breakdown</h3>
                <div class="space-y-4">
                    @foreach($attempt->exam->questions as $index => $question)
                        @php
                            $answer = $attempt->answers->firstWhere('question_id', $question->id);
                        @endphp
                        <div class="p-4 rounded-lg @if($answer && $answer->is_correct) bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 @elseif($answer && !$answer->is_correct) bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50 @else bg-gray-50 dark:bg-slate-700/50 @endif">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-slate-200">Q{{ $index + 1 }}. {{ $question->question_text }}</p>
                                    @if($answer && $answer->selectedOption)
                                        <p class="text-sm mt-1 @if($answer->is_correct) text-emerald-700 dark:text-emerald-300 @else text-red-700 dark:text-red-300 @endif">
                                            Your answer: {{ $answer->selectedOption->option_text }}
                                        </p>
                                    @elseif($answer && $answer->answer_text)
                                        <p class="text-sm mt-1 text-gray-700 dark:text-slate-300">Your answer: {{ $answer->answer_text }}</p>
                                    @else
                                        <p class="text-sm mt-1 text-gray-400 dark:text-slate-500 italic">Not answered</p>
                                    @endif
                                    @if($answer && $answer->feedback)
                                        <p class="text-xs mt-1 text-gray-500 dark:text-slate-400">Feedback: {{ $answer->feedback }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0 ml-4">
                                    <span class="text-sm font-bold @if($answer && $answer->marks_obtained > 0) text-emerald-600 @else text-red-500 @endif">
                                        {{ $answer->marks_obtained ?? 0 }} / {{ $question->pivot->marks ?? $question->default_marks }}
                                    </span>
                                </div>
                            </div>
                            @if($question->explanation)
                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-slate-600">
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Explanation: {{ $question->explanation }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
