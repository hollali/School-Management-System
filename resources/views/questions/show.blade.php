@section('title', 'View Question')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Question Details</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $question->subject?->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('questions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                @if(Auth::user()->hasRole('Teacher'))
                    <a href="{{ route('questions.edit', $question) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition shadow-sm">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-start gap-2 mb-6">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300 shrink-0 mt-0.5">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($question->difficulty === 'easy') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30
                    @elseif($question->difficulty === 'medium') bg-amber-100 text-amber-700 dark:bg-amber-900/30
                    @else bg-red-100 text-red-700 dark:bg-red-900/30 @endif
                    dark:text-${question.difficulty}-300 shrink-0 mt-0.5">{{ ucfirst($question->difficulty) }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300 shrink-0 mt-0.5">{{ $question->default_marks }} marks</span>
                @if($question->topic)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400 shrink-0 mt-0.5">{{ $question->topic }}</span>
                @endif
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-2">Question</p>
                <p class="text-lg font-medium text-gray-900 dark:text-slate-200">{{ $question->question_text }}</p>
            </div>

            @if($question->options->count() > 0)
                <div class="mb-6">
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-3">Answer Options</p>
                    <div class="space-y-2">
                        @foreach($question->options as $option)
                            <div class="flex items-center gap-3 p-3 rounded-lg @if($option->is_correct) bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 @else bg-gray-50 dark:bg-slate-700/50 @endif">
                                <span class="text-sm @if($option->is_correct) text-emerald-700 dark:text-emerald-300 font-semibold @else text-gray-700 dark:text-slate-300 @endif">
                                    @if($option->is_correct) <i class="fa-solid fa-check text-emerald-500 mr-1.5"></i> @endif
                                    {{ $option->option_text }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($question->explanation)
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700/50">
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Explanation</p>
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ $question->explanation }}</p>
                </div>
            @endif

            @if($question->image)
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Attachment</p>
                    @if(in_array(pathinfo($question->image, PATHINFO_EXTENSION), ['jpg','jpeg','png','gif','bmp','svg']))
                        <img src="{{ Storage::url($question->image) }}" alt="Question attachment" class="max-w-md rounded-xl border border-gray-200 dark:border-slate-700">
                    @else
                        <a href="{{ Storage::url($question->image) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            <i class="fa-solid fa-file"></i> View Attachment
                        </a>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $question->subject?->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Created By</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $question->teacher?->user?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</p>
                    <p class="text-sm mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium @if($question->is_active) bg-emerald-100 text-emerald-700 @else bg-gray-100 text-gray-600 @endif">
                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tags</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $question->tags ? implode(', ', $question->tags) : '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
