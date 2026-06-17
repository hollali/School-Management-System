@section('title', 'Admin Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Admin Dashboard</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Welcome back, {{ Auth::user()->name }}</p>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Students</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $stats['students'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-sky-200/50">
                    <i class="fa-solid fa-user-graduate text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Teachers</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $stats['teachers'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-200/50">
                    <i class="fa-solid fa-chalkboard-user text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Classes</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $stats['classes'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-200/50">
                    <i class="fa-solid fa-school text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Parents</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $stats['parents'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-violet-500 flex items-center justify-center shadow-lg shadow-purple-200/50">
                    <i class="fa-solid fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-1">Today's Overview</h3>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">Quick snapshot of today's activity</p>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-check-to-slot text-sky-600 dark:text-sky-400 text-xs"></i>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-slate-400">Attendance records</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $stats['todayAttendance'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-sack-dollar text-amber-600 dark:text-amber-400 text-xs"></i>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-slate-400">Pending fees</span>
                    </div>
                    <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">{{ $stats['pendingFees'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-file-pen text-emerald-600 dark:text-emerald-400 text-xs"></i>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-slate-400">Active assignments</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $stats['pendingAssignments'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-message text-violet-600 dark:text-violet-400 text-xs"></i>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-slate-400">Active conversations</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $stats['activeConversations'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 bg-sky-50 dark:bg-sky-900/30 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-800/40 transition border border-sky-100 dark:border-sky-800/40">
                    <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-sky-700 dark:text-sky-300">Add User</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/40 transition border border-emerald-100 dark:border-emerald-800/40">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center">
                        <i class="fa-solid fa-users-gear text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Manage Users</span>
                </a>
                <a href="{{ route('classes.index') }}" class="flex items-center gap-3 p-3 bg-amber-50 dark:bg-amber-900/30 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-800/40 transition border border-amber-100 dark:border-amber-800/40">
                    <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center">
                        <i class="fa-solid fa-school text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">Classes</span>
                </a>
                <a href="{{ route('subjects.index') }}" class="flex items-center gap-3 p-3 bg-violet-50 dark:bg-violet-900/30 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-800/40 transition border border-violet-100 dark:border-violet-800/40">
                    <div class="w-9 h-9 rounded-lg bg-violet-500 flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-violet-700 dark:text-violet-300">Subjects</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-1">System Overview</h3>
        <p class="text-xs text-gray-400 dark:text-slate-500 mb-4">School management summary</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200">{{ $stats['students'] + $stats['teachers'] + $stats['parents'] }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Total Users</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200">{{ $stats['pendingFees'] }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Pending Fees</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200">{{ $stats['pendingAssignments'] }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Active Assignments</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg dark:bg-slate-700/50">
                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200">{{ $stats['todayAttendance'] }}</p>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Today's Attendance</p>
            </div>
        </div>
    </div>
</x-app-layout>
