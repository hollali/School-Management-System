<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Submission Details') }}</h2>
            <div class="flex items-center gap-2">
                @if(Auth::user()->hasRole('Teacher'))
                    <a href="{{ route('submissions.edit', $submission) }}" title="Edit"
                        class="inline-flex items-center justify-center w-9 h-9 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endif
                <a href="{{ route('submissions.index') }}" title="Back to list"
                    class="inline-flex items-center justify-center w-9 h-9 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Assignment</p>
                        <p class="text-sm text-gray-900 font-medium mt-1">{{ $submission->assignment->title }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Student</p>
                        <p class="text-sm text-gray-900 font-medium mt-1">{{ $submission->student->user->name }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Submitted At</p>
                        <p class="text-sm text-gray-900 font-medium mt-1">{{ $submission->submitted_at?->format('M d, Y H:i') ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="mt-1">
                            @php
                                $subStatusColors = [
                                    'submitted' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'graded' => 'bg-blue-100 text-blue-700',
                                ];
                                $subColor = $subStatusColors[$submission->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $subColor }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($submission->content)
                    <div class="mt-6 bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Content</p>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $submission->content }}</p>
                    </div>
                @endif

                @if($submission->attachment_path)
                    <div class="mt-6 bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Attachment</p>
                        <div class="mt-2">
                            <a href="{{ Storage::url($submission->attachment_path) }}" target="_blank"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download attachment
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Feedback</h3>

                @if($submission->feedback)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50/50 rounded-xl p-6">
                            <p class="text-sm text-gray-500">Score</p>
                            <p class="text-sm text-gray-900 font-medium mt-1">{{ $submission->feedback->score ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50/50 rounded-xl p-6">
                            <p class="text-sm text-gray-500">Teacher</p>
                            <p class="text-sm text-gray-900 font-medium mt-1">{{ $submission->feedback?->teacher?->user?->name ?? '—' }}</p>
                        </div>
                    </div>
                    @if($submission->feedback->comments)
                        <div class="mt-6 bg-gray-50/50 rounded-xl p-6">
                            <p class="text-sm text-gray-500">Comments</p>
                            <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $submission->feedback->comments }}</p>
                        </div>
                    @endif
                    @if(Auth::user()->hasRole('Teacher'))
                        <div class="mt-6">
                            <a href="{{ route('assignment-feedback.edit', $submission->feedback) }}" title="Edit feedback"
                                class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-sky-600 to-cyan-600 text-white rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50/50 rounded-xl p-6 text-center">
                        <p class="text-sm text-gray-500">No feedback yet.</p>
                        @if(Auth::user()->hasRole('Teacher'))
                            <div class="mt-4">
                                <a href="{{ route('assignment-feedback.create') }}" title="Add feedback"
                                    class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-sky-600 to-cyan-600 text-white rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
