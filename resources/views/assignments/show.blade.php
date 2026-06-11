<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ $assignment->title }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('assignments.edit', $assignment) }}"
                    class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('assignments.index') }}"
                    class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    {{ __('Back to list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Class</p>
                        <p class="text-sm text-gray-900 font-medium mt-1">{{ $assignment->schoolClass?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Subject</p>
                        <p class="text-sm text-gray-900 font-medium mt-1">{{ $assignment->subject?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Teacher</p>
                        <p class="text-sm text-gray-900 font-medium mt-1">{{ $assignment->teacher?->user?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Due Date</p>
                        <p class="text-sm text-gray-900 font-medium mt-1">
                            @if($assignment->due_date)
                                {{ $assignment->due_date->format('M d, Y') }}
                                @php
                                    $isOverdue = $assignment->due_date->isPast();
                                    $daysLeft = now()->diffInDays($assignment->due_date, false);
                                @endphp
                                @if($daysLeft > 0 && !$isOverdue)
                                    <span class="text-xs text-gray-400">({{ round($daysLeft) }} days left)</span>
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
                    <div class="mt-6 bg-gray-50/50 rounded-xl p-6">
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $assignment->description }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Submissions ({{ $assignment->submissions->count() }})</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted At</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($assignment->submissions as $submission)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->feedback?->score ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('submissions.show', $submission) }}" class="text-sky-600 hover:text-sky-800 font-medium">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">No submissions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
