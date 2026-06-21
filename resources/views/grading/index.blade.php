@section('title', 'Grading')

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Grading</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Review and grade student exam submissions</p>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Pending Grading --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-amber-700 dark:text-amber-300 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock"></i> Pending Grading ({{ $pendingGrading->total() }})
            </h3>
            @if($pendingGrading->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingGrading as $attempt)
                        <div class="flex items-center justify-between p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-700/50">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $attempt->student->user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $attempt->exam->name }} • {{ $attempt->exam->subject?->name }} • {{ $attempt->submitted_at?->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('grading.grade', $attempt) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-xl hover:bg-amber-700 transition shadow-sm">
                                <i class="fa-solid fa-pen"></i> Grade
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $pendingGrading->links() }}</div>
            @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-check-circle text-3xl text-emerald-300 dark:text-emerald-600 mb-2"></i>
                    <p class="text-sm text-gray-400 dark:text-slate-500">No pending submissions to grade. Great job!</p>
                </div>
            @endif
        </div>

        {{-- Recently Graded --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Recently Graded</h3>
            @if($graded->count() > 0)
                <div class="space-y-2">
                    @foreach($graded as $attempt)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $attempt->student->user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $attempt->exam->name }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold @if($attempt->result_status === 'pass') text-emerald-600 @else text-red-600 @endif">{{ $attempt->percentage_score }}%</span>
                                <span class="text-xs @if($attempt->result_status === 'pass') text-emerald-600 @else text-red-600 @endif">{{ ucfirst($attempt->result_status) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $graded->links() }}</div>
            @else
                <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No graded submissions yet.</p>
            @endif
        </div>
    </div>
</x-app-layout>
