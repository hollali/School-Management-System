@section('title', 'My Exams')

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">My Exams</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">View upcoming exams and take online exams</p>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Available Online Exams --}}
        @if($availableExams->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-emerald-700 dark:text-emerald-300 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-play-circle"></i> Available Online Exams
                </h3>
                <div class="grid gap-3">
                    @foreach($availableExams as $exam)
                        <div class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-700/50">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $exam->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $exam->subject?->name }} • {{ $exam->duration_minutes }} min • {{ $exam->total_marks }} marks</p>
                            </div>
                            <a href="{{ route('student.exams.start', $exam) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                                <i class="fa-solid fa-arrow-right"></i> Start Exam
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Upcoming Exams --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-calendar"></i> Upcoming Exams
            </h3>
            @if($upcomingExams->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingExams as $exam)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $exam->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $exam->subject?->name }} • {{ $exam->start_date?->format('M d, Y H:i') }} • {{ $exam->duration_minutes }} min</p>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium @if($exam->exam_mode === 'online') bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400 @else bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 @endif">
                                {{ ucfirst($exam->exam_mode) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-calendar text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                    <p class="text-sm text-gray-400 dark:text-slate-500">No upcoming exams.</p>
                </div>
            @endif
        </div>

        {{-- Exam History --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-slate-200">My Exam History</h3>
                <a href="{{ route('student.exams.history') }}" class="text-xs text-sky-600 hover:text-sky-800 font-medium">View all</a>
            </div>
            @if($attempts->count() > 0)
                <div class="space-y-3">
                    @foreach($attempts as $attempt)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $attempt->exam->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $attempt->exam->subject?->name }} • {{ $attempt->submitted_at?->format('M d, Y H:i') ?? 'Not submitted' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($attempt->status === 'graded' && $attempt->exam->results_published)
                                    <span class="text-sm font-bold @if($attempt->result_status === 'pass') text-emerald-600 @else text-red-600 @endif">{{ $attempt->percentage_score }}%</span>
                                    <a href="{{ route('student.exams.result', $attempt) }}" class="text-xs text-sky-600 hover:underline">View</a>
                                @elseif($attempt->status === 'graded')
                                    <span class="text-xs text-amber-600">Awaiting results</span>
                                @elseif($attempt->status === 'submitted')
                                    <span class="text-xs text-amber-600">Pending review</span>
                                @elseif($attempt->status === 'in_progress')
                                    <a href="{{ route('student.exams.start', $attempt->exam) }}" class="text-xs px-3 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Continue</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-clock-rotate-left text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                    <p class="text-sm text-gray-400 dark:text-slate-500">No exam history yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
