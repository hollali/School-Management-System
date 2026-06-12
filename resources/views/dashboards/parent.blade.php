<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-white">Parent Dashboard</h2>
        <p class="text-sm text-white/80 mt-1">Welcome, {{ Auth::user()->name }}</p>
    </x-slot>

    @if($children->count())
        <div class="space-y-6">
            @foreach($children as $child)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex items-center gap-4 p-5 bg-gradient-to-r from-sky-50 to-white border-b border-gray-100">
                        <img src="{{ $child->user?->profile_photo_url ?? '' }}" alt="" class="w-12 h-12 rounded-full">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $child->user?->name ?? 'Unknown' }}</h3>
                            <p class="text-sm text-gray-400">Admission: {{ $child->admission_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Classes</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">{{ $child->classes->count() }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Attendance</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">{{ $child->attendanceRecords()->count() }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Results</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">{{ $child->results()->count() }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('students.show', $child) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-xs font-semibold hover:bg-sky-200 transition">
                                <i class="fa-solid fa-eye"></i> View Profile
                            </a>
                            <a href="{{ route('attendances.index') }}?student_id={{ $child->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold hover:bg-emerald-200 transition">
                                <i class="fa-solid fa-check-to-slot"></i> Attendance
                            </a>
                            <a href="{{ route('results.index') }}?student_id={{ $child->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold hover:bg-amber-200 transition">
                                <i class="fa-solid fa-chart-simple"></i> Results
                            </a>
                            <a href="{{ route('fees.index') }}?student_id={{ $child->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-100 text-violet-700 rounded-lg text-xs font-semibold hover:bg-violet-200 transition">
                                <i class="fa-solid fa-sack-dollar"></i> Fees
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fa-solid fa-children text-5xl text-gray-200 mb-4"></i>
            <h3 class="text-lg font-bold text-gray-900 mb-2">No children linked</h3>
            <p class="text-sm text-gray-400">There are no students linked to your account. Please contact the school administration.</p>
        </div>
    @endif

    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-bold text-gray-900 mb-4">Quick Links</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 p-3 bg-sky-50 rounded-xl hover:bg-sky-100 transition border border-sky-100">
                <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center">
                    <i class="fa-solid fa-message text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-sky-700">Messages</span>
            </a>
            <a href="{{ route('fees.index') }}" class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition border border-emerald-100">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-emerald-700">Fee Info</span>
            </a>
            <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl hover:bg-amber-100 transition border border-amber-100">
                <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center">
                    <i class="fa-solid fa-file-pen text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-amber-700">Homework</span>
            </a>
            <a href="{{ route('results.index') }}" class="flex items-center gap-3 p-3 bg-violet-50 rounded-xl hover:bg-violet-100 transition border border-violet-100">
                <div class="w-9 h-9 rounded-lg bg-violet-500 flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-violet-700">Reports</span>
            </a>
        </div>
    </div>
</x-app-layout>
