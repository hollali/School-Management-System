@section('title', 'Exam Reports')

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Exam Reports & Analytics</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Performance insights and examination statistics</p>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Exams</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_exams'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center"><i class="fa-solid fa-pen-to-square text-sky-600"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Attempts</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_attempts'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center"><i class="fa-solid fa-users text-emerald-600"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Pass Rate</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                            {{ $stats['total_attempts'] > 0 ? round(($stats['pass_count'] / max($stats['total_attempts'], 1)) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center"><i class="fa-solid fa-check-circle text-emerald-600"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Questions Bank</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_questions'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center"><i class="fa-solid fa-database text-violet-600"></i></div>
                </div>
            </div>
        </div>

        {{-- Top Students --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Top Performing Students</h3>
                @if($topStudents->count() > 0)
                    <div class="space-y-2">
                        @foreach($topStudents as $index => $student)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-gray-400 dark:text-slate-500 w-6">#{{ $index + 1 }}</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $student->user->name }}</span>
                                </div>
                                <span class="text-sm font-bold text-emerald-600">{{ round($student->avg_score, 1) }}%</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No data available.</p>
                @endif
            </div>

            {{-- Exam Performance --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Exam Performance</h3>
                @if($examPerformance->count() > 0)
                    <div class="space-y-2">
                        @foreach($examPerformance as $exam)
                            <a href="{{ route('exam-reports.exam-detail', $exam) }}" class="block p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $exam->name }}</p>
                                    <span class="text-xs text-gray-500 dark:text-slate-400">{{ $exam->total_attempts }} attempts</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">{{ $exam->pass_count }} passed</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">{{ $exam->fail_count }} failed</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No graded exams yet.</p>
                @endif
            </div>
        </div>

        {{-- Recent Exams --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Recent Exams</h3>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($recentExams as $exam)
                    <a href="{{ route('exams.show', $exam) }}" class="block p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $exam->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">{{ $exam->subject?->name ?? '—' }} • {{ $exam->type ?? '—' }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($exam->status === 'published') bg-emerald-100 text-emerald-700 @elseif($exam->status === 'draft') bg-amber-100 text-amber-700 @else bg-gray-100 text-gray-600 @endif">
                                {{ ucfirst($exam->status) }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $exam->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
