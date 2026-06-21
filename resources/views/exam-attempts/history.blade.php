@section('title', 'Exam History')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Exam History</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">All your exam attempts</p>
            </div>
            <a href="{{ route('student.exams') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="History" :data="$attempts">
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Exam</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Started</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($attempts as $attempt)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $attempt->exam->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $attempt->exam->subject?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $attempt->started_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">{{ $attempt->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($attempt->status === 'graded') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                @elseif($attempt->status === 'submitted') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300
                                @elseif($attempt->status === 'in_progress') bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300
                                @else bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400 @endif">
                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold @if($attempt->result_status === 'pass') text-emerald-600 @elseif($attempt->result_status === 'fail') text-red-600 @else text-gray-400 @endif">
                            {{ $attempt->percentage_score !== null ? $attempt->percentage_score . '%' : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($attempt->status === 'graded' && $attempt->exam->results_published)
                                <a href="{{ route('student.exams.result', $attempt) }}" class="text-sky-600 hover:text-sky-800 text-sm font-medium">View Result</a>
                            @elseif($attempt->status === 'in_progress')
                                <a href="{{ route('student.exams.start', $attempt->exam) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Continue</a>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-12 text-gray-400 dark:text-slate-500">No exam history.</td></tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>
</x-app-layout>
