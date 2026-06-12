<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-white">Admin Dashboard</h2>
        <p class="text-sm text-white/80 mt-1">Welcome back, {{ Auth::user()->name }}</p>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Students</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['students'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-sky-200/50">
                    <i class="fa-solid fa-user-graduate text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Teachers</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['teachers'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-200/50">
                    <i class="fa-solid fa-chalkboard-user text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Classes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['classes'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-200/50">
                    <i class="fa-solid fa-school text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Parents</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['parents'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-violet-500 flex items-center justify-center shadow-lg shadow-purple-200/50">
                    <i class="fa-solid fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-1">Today's Overview</h3>
            <p class="text-xs text-gray-400 mb-4">Quick snapshot of today's activity</p>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">Attendance records</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $stats['todayAttendance'] }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">Pending fees</span>
                    <span class="text-sm font-semibold text-amber-600">{{ $stats['pendingFees'] }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">Active assignments</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $stats['pendingAssignments'] }}</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-gray-600">Active conversations</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $stats['activeConversations'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.users.create') }}" class="flex items-center gap-3 p-3 bg-sky-50 rounded-xl hover:bg-sky-100 transition border border-sky-100">
                    <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-sky-700">Add User</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition border border-emerald-100">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center">
                        <i class="fa-solid fa-users-gear text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-emerald-700">Manage Users</span>
                </a>
                <a href="{{ route('classes.index') }}" class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl hover:bg-amber-100 transition border border-amber-100">
                    <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center">
                        <i class="fa-solid fa-school text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-amber-700">Classes</span>
                </a>
                <a href="{{ route('subjects.index') }}" class="flex items-center gap-3 p-3 bg-violet-50 rounded-xl hover:bg-violet-100 transition border border-violet-100">
                    <div class="w-9 h-9 rounded-lg bg-violet-500 flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-violet-700">Subjects</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
