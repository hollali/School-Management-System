<x-app-layout>
    @section('title', __('Feedback Details'))

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ __('Feedback Details') }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Assignment feedback overview</p>
            </div>
            <a href="{{ route('assignment-feedback.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Back to list') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden p-8">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ $assignmentFeedback->submission->assignment->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">by {{ $assignmentFeedback->submission->student->user->name }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold
                            @if($assignmentFeedback->score >= 80) bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300
                            @elseif($assignmentFeedback->score >= 60) bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300
                            @else bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300
                            @endif">
                            {{ $assignmentFeedback->score ?? '—' }}<span class="text-xs font-normal ml-0.5">/100</span>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 pb-6 border-b border-gray-100 dark:border-slate-700">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Teacher</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-slate-200">{{ $assignmentFeedback->teacher?->user?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-slate-200">{{ $assignmentFeedback->created_at->format('F d, Y') }}</p>
                    </div>
                </div>

                @if($assignmentFeedback->comments)
                    <div class="mt-6">
                        <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Comments</p>
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-sm text-gray-800 dark:text-slate-200 whitespace-pre-wrap leading-relaxed">
                            {{ $assignmentFeedback->comments }}
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('submissions.show', $assignmentFeedback->submission_id) }}"
                        class="inline-flex items-center text-sm text-sky-600 dark:text-sky-400 hover:text-sky-800 font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        {{ __('Back to Submission') }}
                    </a>
                    <div class="flex gap-3">
                        <a href="{{ route('assignment-feedback.index') }}"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            {{ __('Edit Feedback') }}
                        </a>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('assignment-feedback.destroy', $assignmentFeedback) }}',
                            method: 'DELETE',
                            title: 'Delete Feedback',
                            message: 'Delete this feedback? This action cannot be undone.',
                            confirmLabel: 'Delete',
                            confirmClass: 'bg-red-600 hover:bg-red-700'
                        })"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
