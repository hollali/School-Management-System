@section('title', 'Student Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Student Dashboard</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Welcome, {{ Auth::user()->name }}</p>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Subjects</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $stats['subjects'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-sky-200/50">
                    <i class="fa-solid fa-book-open text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Assignments</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $stats['assignments'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-200/50">
                    <i class="fa-solid fa-file-pen text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Attendance Records</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $stats['attendance'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-200/50">
                    <i class="fa-solid fa-check-to-slot text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">My Classes</h3>
            @if($classes->count())
                <div class="space-y-2">
                    @foreach($classes as $class)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-school text-sky-600 dark:text-sky-400 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $class->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500">{{ $class->teacher?->user?->name ?? 'No teacher assigned' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-school text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                    <p class="text-sm text-gray-400 dark:text-slate-500">No classes assigned yet.</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Recent Results</h3>
            @if($results->count())
                <div class="space-y-2">
                    @foreach($results as $result)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $result->subject?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500">{{ $result->exam?->name ?? 'N/A' }}</p>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-slate-200">{{ $result->score ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-chart-simple text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                    <p class="text-sm text-gray-400 dark:text-slate-500">No results published yet.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Upcoming Assignments</h3>
        @if($recentAssignments->count())
            <div class="space-y-2">
                @foreach($recentAssignments as $assignment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-file-lines text-emerald-600 dark:text-emerald-400 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $assignment->title }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500">Due {{ $assignment->due_date?->format('M d, Y') ?? 'No date' }}</p>
                            </div>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            @if($assignment->due_date?->isPast()) bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400
                            @else bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400
                            @endif">
                            {{ $assignment->due_date?->isPast() ? 'Overdue' : 'Active' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No assignments posted yet.</p>
        @endif
    </div>
</x-app-layout>
