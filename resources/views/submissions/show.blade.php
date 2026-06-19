<x-app-layout>
    @section('title', __('Submission Details'))

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ __('Submission Details') }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Submission details and feedback</p>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $submission)
                    <button @click="$dispatch('open-modal', 'edit-submission')"
                        class="inline-flex items-center justify-center w-9 h-9 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-slate-500 dark:hover:text-slate-400 dark:hover:bg-slate-600 rounded-xl transition"
                        title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                @endcan
                <a href="{{ route('submissions.index') }}" title="Back to list"
                    class="inline-flex items-center justify-center w-9 h-9 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-slate-500 dark:hover:text-slate-400 dark:hover:bg-slate-600 rounded-xl transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Assignment</p>
                        <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $submission->assignment->title }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Student</p>
                        <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $submission->student->user->name }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Submitted At</p>
                        <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $submission->submitted_at?->format('M d, Y H:i') ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Status</p>
                        <p class="mt-1">
                            @php
                                $subStatusColors = [
                                    'submitted' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200',
                                    'graded' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                    'retracted' => 'bg-gray-100 text-gray-700 dark:bg-slate-600 dark:text-slate-300',
                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $subColor = $subStatusColors[$submission->status] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700/50 dark:text-slate-300';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $subColor }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($submission->content)
                    <div class="mt-6 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Content</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap">{{ $submission->content }}</p>
                    </div>
                @endif

                @if($submission->status === 'rejected' && $submission->rejection_reason)
                    <div class="mt-6 bg-red-50 dark:bg-red-900/10 rounded-xl p-6 border border-red-200 dark:border-red-900/20">
                        <p class="text-sm font-semibold text-red-600 dark:text-red-400">Rejection Reason</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-slate-200">{{ $submission->rejection_reason }}</p>
                    </div>
                @endif

                @if($submission->attachment_path)
                    <div class="mt-6 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Attachment</p>
                        <div class="mt-2">
                            <a href="{{ Storage::url($submission->attachment_path) }}" target="_blank"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-slate-700/50 text-gray-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download attachment
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex gap-3">
                @can('retract', $submission)
                    <form action="{{ route('submissions.retract', $submission) }}" method="POST" class="inline" onsubmit="return confirm('Withdraw this submission? You can resubmit later.')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            <i class="fa-solid fa-rotate-left"></i> Withdraw Submission
                        </button>
                    </form>
                @endcan
                @can('reject', $submission)
                    <button @click="$dispatch('reject-submission', @js(['id' => $submission->id, 'student' => $submission->student->user->name])); $dispatch('open-modal', 'reject-submission')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fa-solid fa-xmark"></i> Reject Submission
                    </button>
                @endcan
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Feedback</h3>

                @if($submission->feedback)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                            <p class="text-sm text-gray-500 dark:text-slate-400">Score</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $submission->feedback->score ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                            <p class="text-sm text-gray-500 dark:text-slate-400">Teacher</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $submission->feedback?->teacher?->user?->name ?? '—' }}</p>
                        </div>
                    </div>
                    @if($submission->feedback->comments)
                        <div class="mt-6 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                            <p class="text-sm text-gray-500 dark:text-slate-400">Comments</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap">{{ $submission->feedback->comments }}</p>
                        </div>
                    @endif
                    @can('update', $submission->feedback)
                        <div class="mt-6">
                            <a href="{{ route('assignment-feedback.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white text-sm font-semibold rounded-xl hover:from-amber-700 hover:to-orange-700 transition shadow-sm">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                Edit Feedback
                            </a>
                        </div>
                    @endcan
                @else
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6 text-center">
                        <p class="text-sm text-gray-500 dark:text-slate-400">No feedback yet.</p>
                        @can('create', App\Models\AssignmentFeedback::class)
                            <div class="mt-4">
                                <a href="{{ route('assignment-feedback.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                    <i class="fa-solid fa-plus text-sm"></i>
                                    Add Feedback
                                </a>
                            </div>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="reject-submission" maxWidth="lg" focusable>
        <div class="p-6" x-data="rejectSubmissionData()" @reject-submission.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Reject Submission</h2>
                <button @click="$dispatch('close-modal', 'reject-submission')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/submissions/${form.id}/reject`">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-slate-400">Rejecting submission for: <strong x-text="form.student"></strong></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Reason for Rejection</label>
                    <textarea name="rejection_reason" rows="4" required
                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"
                        placeholder="Explain why the submission is being rejected..."></textarea>
                    @error('rejection_reason')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button @click="$dispatch('close-modal', 'reject-submission')" type="button"
                        class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 text-white text-sm font-semibold rounded-xl hover:from-red-700 hover:to-rose-700 transition shadow-sm">
                        Reject Submission
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
    function rejectSubmissionData() {
        return {
            form: { id: '', student: '' },
            load(data) {
                this.form = { ...data };
            }
        };
    }
    </script>
</x-app-layout>
