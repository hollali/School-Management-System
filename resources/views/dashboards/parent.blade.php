@section('title', 'Parent Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Parent Dashboard</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Welcome, {{ Auth::user()->name }}</p>
    </x-slot>

    @if($children->count())
        <div class="space-y-6">
            @foreach($children as $child)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
                    <div class="flex items-center gap-4 p-5 bg-gradient-to-r from-sky-50 to-white dark:from-sky-900/20 dark:to-slate-800 border-b border-gray-100 dark:border-slate-700">
                        <img src="{{ $child->user?->profile_photo_url ?? '' }}" alt="" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200">{{ $child->user?->name ?? 'Unknown' }}</h3>
                            <p class="text-sm text-gray-400 dark:text-slate-500">Admission: {{ $child->admission_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                                <div class="w-10 h-10 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center mx-auto mb-2">
                                    <i class="fa-solid fa-school text-sky-600 dark:text-sky-400"></i>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider font-medium">Classes</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $child->classes->count() }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-2">
                                    <i class="fa-solid fa-check-to-slot text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider font-medium">Attendance</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $child->attendanceRecords()->count() }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 text-center">
                                <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-2">
                                    <i class="fa-solid fa-chart-simple text-amber-600 dark:text-amber-400"></i>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider font-medium">Results</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $child->results()->count() }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('students.show', $child) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-xs font-semibold hover:bg-sky-200 transition">
                                <i class="fa-solid fa-eye"></i> View Profile
                            </a>
                            <a href="{{ route('attendances.index') }}?student_id={{ $child->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-lg text-xs font-semibold hover:bg-emerald-200 transition">
                                <i class="fa-solid fa-check-to-slot"></i> Attendance
                            </a>
                            <a href="{{ route('results.index') }}?student_id={{ $child->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-lg text-xs font-semibold hover:bg-amber-200 transition">
                                <i class="fa-solid fa-chart-simple"></i> Results
                            </a>
                            <a href="{{ route('fees.index') }}?student_id={{ $child->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-lg text-xs font-semibold hover:bg-violet-200 transition">
                                <i class="fa-solid fa-sack-dollar"></i> Fees
                            </a>
                            <a href="{{ route('assignments.index') }}?student_id={{ $child->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded-lg text-xs font-semibold hover:bg-rose-200 dark:hover:bg-rose-800/40 transition">
                                <i class="fa-solid fa-file-pen"></i> Assignments
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-12 text-center">
            <i class="fa-solid fa-children text-5xl text-gray-200 dark:text-slate-600 mb-4"></i>
            <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200 mb-2">No children linked</h3>
            <p class="text-sm text-gray-400 dark:text-slate-500">There are no students linked to your account. Please contact the school administration.</p>
        </div>
    @endif

    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Quick Links</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 p-3 bg-sky-50 dark:bg-sky-900/30 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-800/40 transition border border-sky-100 dark:border-sky-800/40">
                <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center">
                    <i class="fa-solid fa-message text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-sky-700 dark:text-sky-300">Messages</span>
            </a>
            <a href="{{ route('fees.index') }}" class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/40 transition border border-emerald-100 dark:border-emerald-800/40">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Fee Info</span>
            </a>
            <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 p-3 bg-amber-50 dark:bg-amber-900/30 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-800/40 transition border border-amber-100 dark:border-amber-800/40">
                <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center">
                    <i class="fa-solid fa-file-pen text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">Homework</span>
            </a>
            <a href="{{ route('results.index') }}" class="flex items-center gap-3 p-3 bg-violet-50 dark:bg-violet-900/30 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-800/40 transition border border-violet-100 dark:border-violet-800/40">
                <div class="w-9 h-9 rounded-lg bg-violet-500 flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-violet-700 dark:text-violet-300">Reports</span>
            </a>
        </div>
    </div>
</x-app-layout>
