<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Feedback Details') }}</h2>
            <a href="{{ route('assignment-feedback.index') }}"
                class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Back to list') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden p-8">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $assignmentFeedback->submission->assignment->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500">by {{ $assignmentFeedback->submission->student->user->name }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold
                            @if($assignmentFeedback->score >= 80) bg-emerald-100 text-emerald-700
                            @elseif($assignmentFeedback->score >= 60) bg-amber-100 text-amber-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $assignmentFeedback->score ?? '—' }}<span class="text-xs font-normal ml-0.5">/100</span>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Teacher</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $assignmentFeedback->teacher?->user?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $assignmentFeedback->created_at->format('F d, Y') }}</p>
                    </div>
                </div>

                @if($assignmentFeedback->comments)
                    <div class="mt-6">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Comments</p>
                        <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-800 whitespace-pre-wrap leading-relaxed">
                            {{ $assignmentFeedback->comments }}
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('submissions.show', $assignmentFeedback->submission_id) }}"
                        class="inline-flex items-center text-sm text-sky-600 hover:text-sky-800 font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        {{ __('Back to Submission') }}
                    </a>
                    <div class="flex gap-3">
                        <a href="{{ route('assignment-feedback.edit', $assignmentFeedback) }}"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            {{ __('Edit Feedback') }}
                        </a>
                        <form action="{{ route('assignment-feedback.destroy', $assignmentFeedback) }}" method="POST"
                            onsubmit="return confirm('Delete this feedback?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
