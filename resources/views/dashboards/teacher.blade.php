<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-white">Teacher Dashboard</h2>
        <p class="text-sm text-white/80 mt-1">Welcome back, {{ Auth::user()->name }}</p>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">My Classes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['myClasses'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-sky-200/50">
                    <i class="fa-solid fa-chalkboard text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">My Students</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['myStudents'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-200/50">
                    <i class="fa-solid fa-user-graduate text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Assignments</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['myAssignments'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-200/50">
                    <i class="fa-solid fa-file-pen text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Today's Attendance</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['todayAttendance'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center shadow-lg shadow-violet-200/50">
                    <i class="fa-solid fa-check-to-slot text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">My Classes</h3>
                <a href="{{ route('classes.index') }}" class="text-xs text-sky-600 font-medium hover:text-sky-800">View all</a>
            </div>
            @if($classes->count())
                <div class="space-y-2">
                    @foreach($classes as $class)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                                    <i class="fa-solid fa-school text-sky-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $class->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $class->students_count ?? 0 }} students</p>
                                </div>
                            </div>
                            <a href="{{ route('classes.show', $class) }}" class="text-xs text-sky-600 hover:text-sky-800">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No classes assigned yet.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Recent Assignments</h3>
                <a href="{{ route('assignments.index') }}" class="text-xs text-sky-600 font-medium hover:text-sky-800">View all</a>
            </div>
            @if($recentAssignments->count())
                <div class="space-y-2">
                    @foreach($recentAssignments as $assignment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $assignment->title }}</p>
                                <p class="text-xs text-gray-400">Due {{ $assignment->due_date?->format('M d, Y') ?? 'No date' }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                @if($assignment->due_date?->isPast()) bg-red-100 text-red-600
                                @else bg-emerald-100 text-emerald-600
                                @endif">
                                {{ $assignment->due_date?->isPast() ? 'Overdue' : 'Active' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No assignments created yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('attendances.create') }}" class="flex items-center gap-3 p-3 bg-sky-50 rounded-xl hover:bg-sky-100 transition border border-sky-100">
                <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center">
                    <i class="fa-solid fa-check-to-slot text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-sky-700">Attendance</span>
            </a>
            <a href="{{ route('assignments.create') }}" class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition border border-emerald-100">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center">
                    <i class="fa-solid fa-file-pen text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-emerald-700">Assignment</span>
            </a>
            <a href="{{ route('exams.create') }}" class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl hover:bg-amber-100 transition border border-amber-100">
                <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center">
                    <i class="fa-solid fa-pen-to-square text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-amber-700">Exam</span>
            </a>
            <a href="{{ route('results.create') }}" class="flex items-center gap-3 p-3 bg-violet-50 rounded-xl hover:bg-violet-100 transition border border-violet-100">
                <div class="w-9 h-9 rounded-lg bg-violet-500 flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple text-white text-sm"></i>
                </div>
                <span class="text-sm font-semibold text-violet-700">Grades</span>
            </a>
        </div>
    </div>
</x-app-layout>
