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

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-4">Submissions ({{ $assignment->submissions->count() }})</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Submitted At</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse($assignment->submissions as $submission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200">{{ $submission->student->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">{{ $submission->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $subStatusColors = [
                                                'submitted' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                                'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200',
                                                'graded' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                            ];
                                            $subColor = $subStatusColors[$submission->status] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700/50 dark:text-slate-300';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $subColor }}">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">{{ $submission->feedback?->score ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('submissions.show', $submission) }}" class="text-sky-600 dark:text-sky-400 hover:text-sky-800 font-medium">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">No submissions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
