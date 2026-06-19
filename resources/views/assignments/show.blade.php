<x-app-layout>
    @section('title', $assignment->title)

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ $assignment->title }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Assignment details and submissions</p>
            </div>
            <div class="flex items-center gap-2">
                @if(Auth::user()->hasRole('Teacher'))
                    <a href="{{ route('assignments.index') }}" title="Edit"
                        class="inline-flex items-center justify-center w-9 h-9 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-slate-500 dark:hover:text-slate-400 dark:hover:bg-slate-600 rounded-xl transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endif
                <a href="{{ route('assignments.index') }}" title="Back to list"
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
                        <p class="text-sm text-gray-500 dark:text-slate-400">Class</p>
                        <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $assignment->schoolClass?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Subject</p>
                        <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $assignment->subject?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Teacher</p>
                        <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $assignment->teacher?->user?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Due Date</p>
                        <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">
                            @if($assignment->due_date)
                                {{ $assignment->due_date->format('M d, Y') }}
                                @php
                                    $isOverdue = $assignment->due_date->isPast();
                                    $daysLeft = now()->diffInDays($assignment->due_date, false);
                                @endphp
                                @if($daysLeft > 0 && !$isOverdue)
                                    <span class="text-xs text-gray-400 dark:text-slate-500">({{ round($daysLeft) }} days left)</span>
                                @elseif($isOverdue)
                                    <span class="text-xs text-red-400">(overdue)</span>
                                @endif
                            @else
                                No due date
                            @endif
                        </p>
                    </div>
                </div>

                @if($assignment->description)
                    <div class="mt-6 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">Description</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap">{{ $assignment->description }}</p>
                    </div>
                @endif
            </div>

            @if(Auth::user()->hasRole('Student'))
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Your Submission</h3>

                    @if($submission)
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Submitted At</p>
                                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $submission->submitted_at?->format('M d, Y H:i') ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Status</p>
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
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $subColor }} mt-1">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($submission->content)
                                <div class="mb-4">
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Content</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap">{{ $submission->content }}</p>
                                </div>
                            @endif

                            @if($submission->attachment_path)
                                <div class="mb-4">
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Attachment</p>
                                    <a href="{{ Storage::url($submission->attachment_path) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 mt-1 text-sm text-sky-600 dark:text-sky-400 hover:underline">
                                        <i class="fa-solid fa-download"></i> Download
                                    </a>
                                </div>
                            @endif

                            @if($submission->status === 'rejected' && $submission->rejection_reason)
                                <div class="border-t border-gray-200 dark:border-slate-600 pt-4 mt-4">
                                    <p class="text-sm font-semibold text-red-600 dark:text-red-400 mb-2">Submission Rejected</p>
                                    <div class="bg-red-50 dark:bg-red-900/10 rounded-xl p-4">
                                        <p class="text-xs text-gray-500 dark:text-slate-400">Reason</p>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-slate-200">{{ $submission->rejection_reason }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($submission->feedback)
                                <div class="border-t border-gray-200 dark:border-slate-600 pt-4 mt-4">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-slate-200 mb-2">Feedback</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">Score</p>
                                            <p class="text-lg font-bold
                                                @if($submission->feedback->score >= 80) text-green-600 dark:text-green-400
                                                @elseif($submission->feedback->score >= 60) text-amber-600 dark:text-amber-400
                                                @else text-red-600 dark:text-red-400
                                                @endif">
                                                {{ $submission->feedback->score }}/100
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">Teacher</p>
                                            <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1">{{ $submission->feedback?->teacher?->user?->name ?? '—' }}</p>
                                        </div>
                                    </div>
                                    @if($submission->feedback->comments)
                                        <div class="mt-3">
                                            <p class="text-xs text-gray-500 dark:text-slate-400">Comments</p>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap">{{ $submission->feedback->comments }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4 flex gap-3">
                                <a href="{{ route('submissions.show', $submission) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                    <i class="fa-solid fa-eye"></i> View Details
                                </a>
                                @can('update', $submission)
                                    <a href="{{ route('submissions.index') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit Submission
                                    </a>
                                @endcan
                                @can('retract', $submission)
                                    <form action="{{ route('submissions.retract', $submission) }}" method="POST" class="inline" onsubmit="return confirm('Withdraw this submission? You can resubmit later.')">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                            <i class="fa-solid fa-rotate-left"></i> Withdraw
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6 text-center">
                            <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">You haven't submitted this assignment yet.</p>
                            @can('create', App\Models\Submission::class)
                                <a href="{{ route('submissions.index') }}?assignment_id={{ $assignment->id }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                                    <i class="fa-solid fa-upload"></i> Submit Assignment
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Submissions ({{ $assignment->submissions->count() }})</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">File</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Submitted At</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @forelse($assignment->submissions as $s)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200">{{ $s->student->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                            @if($s->attachment_path)
                                                @php
                                                    $ext = pathinfo($s->attachment_path, PATHINFO_EXTENSION);
                                                    $icon = match(strtolower($ext)) {
                                                        'pdf' => 'fa-file-pdf',
                                                        'doc','docx' => 'fa-file-word',
                                                        'xls','xlsx' => 'fa-file-excel',
                                                        'ppt','pptx' => 'fa-file-powerpoint',
                                                        'jpg','jpeg','png','gif','bmp','svg','webp' => 'fa-file-image',
                                                        'mp4','avi','mov','wmv','webm','mkv','flv' => 'fa-file-video',
                                                        'zip' => 'fa-file-zipper',
                                                        default => 'fa-file',
                                                    };
                                                @endphp
                                                <i class="fa-solid {{ $icon }} text-lg text-gray-400" title="{{ strtoupper($ext) }} file"></i>
                                            @else
                                                <span class="text-gray-300 dark:text-slate-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">{{ $s->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $subStatusColors = [
                                                    'submitted' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200',
                                                    'graded' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'retracted' => 'bg-gray-100 text-gray-700 dark:bg-slate-600 dark:text-slate-300',
                                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                                ];
                                                $subColor = $subStatusColors[$s->status] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700/50 dark:text-slate-300';
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $subColor }}">
                                                {{ ucfirst($s->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">{{ $s->feedback?->score ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('submissions.show', $s) }}" class="text-sky-600 dark:text-sky-400 hover:text-sky-800 font-medium">View</a>
                                            @can('create', App\Models\AssignmentFeedback::class)
                                                @if(!$s->feedback && $s->status === 'submitted')
                                                    <a href="{{ route('assignment-feedback.index') }}?submission_id={{ $s->id }}"
                                                        class="text-amber-600 dark:text-amber-400 hover:text-amber-800 font-medium">Grade</a>
                                                @elseif($s->feedback)
                                                    <a href="{{ route('assignment-feedback.index') }}"
                                                        class="text-amber-600 dark:text-amber-400 hover:text-amber-800 font-medium">Edit Grade</a>
                                                @endif
                                            @endcan
                                            @can('reject', $s)
                                                <button @click="$dispatch('reject-submission', @js(['id' => $s->id, 'student' => $s->student->user->name])); $dispatch('open-modal', 'reject-submission')"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-800 font-medium">Reject</button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">No submissions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
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
