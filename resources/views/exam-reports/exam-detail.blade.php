@section('title', $exam->name . ' - Report')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ $exam->name }} - Report</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $exam->subject?->name }} • {{ $exam->class?->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('exam-reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('exam-reports.export-csv', ['exam_id' => $exam->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                    <i class="fa-solid fa-download"></i> Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Passed</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $passCount }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Failed</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $failCount }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Average Score</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $avgScore ? round($avgScore, 1) . '%' : '—' }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Pass Rate</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $attempts->count() > 0 ? round(($passCount / $attempts->count()) * 100, 1) . '%' : '—' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Scores</h3>
                <div class="space-y-2">
                    <div class="flex justify-between p-2 bg-gray-50 dark:bg-slate-700/50 rounded"><span class="text-sm text-gray-600">Highest</span><span class="text-sm font-bold text-emerald-600">{{ $highestScore ? round($highestScore, 1) . '%' : '—' }}</span></div>
                    <div class="flex justify-between p-2 bg-gray-50 dark:bg-slate-700/50 rounded"><span class="text-sm text-gray-600">Lowest</span><span class="text-sm font-bold text-red-600">{{ $lowestScore ? round($lowestScore, 1) . '%' : '—' }}</span></div>
                    <div class="flex justify-between p-2 bg-gray-50 dark:bg-slate-700/50 rounded"><span class="text-sm text-gray-600">Average</span><span class="text-sm font-bold text-sky-600">{{ $avgScore ? round($avgScore, 1) . '%' : '—' }}</span></div>
                    <div class="flex justify-between p-2 bg-gray-50 dark:bg-slate-700/50 rounded"><span class="text-sm text-gray-600">Total Students</span><span class="text-sm font-bold text-gray-900">{{ $attempts->count() }}</span></div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Question Analysis</h3>
                @if(count($questionAnalysis) > 0)
                    <div class="space-y-2">
                        @foreach($questionAnalysis as $qa)
                            <div class="p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm text-gray-900 dark:text-slate-200 truncate">{{ Str::limit($qa['question']->question_text, 60) }}</p>
                                    <span class="text-xs font-bold @if($qa['accuracy'] >= 70) text-emerald-600 @elseif($qa['accuracy'] >= 40) text-amber-600 @else text-red-600 @endif">{{ $qa['accuracy'] }}%</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span>{{ $qa['correct_count'] }}/{{ $qa['total_count'] }} correct</span>
                                    <div class="flex-1 h-1.5 bg-gray-200 dark:bg-slate-600 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full @if($qa['accuracy'] >= 70) bg-emerald-500 @elseif($qa['accuracy'] >= 40) bg-amber-500 @else bg-red-500 @endif"
                                            style="width: {{ $qa['accuracy'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No question data available.</p>
                @endif
            </div>
        </div>

        {{-- Student List --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Student Results</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Score</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Percentage</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Result</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($attempts as $attempt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-slate-200">{{ $attempt->student->user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-slate-300">{{ $attempt->total_score }}</td>
                                <td class="px-4 py-3 text-sm font-bold @if($attempt->result_status === 'pass') text-emerald-600 @else text-red-600 @endif">{{ $attempt->percentage_score }}%</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium @if($attempt->result_status === 'pass') bg-emerald-100 text-emerald-700 @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($attempt->result_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-slate-400">{{ $attempt->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-gray-400">No graded attempts.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
