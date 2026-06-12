@php
$user = Auth::user();
$isAdmin = $user->hasRole('Admin');
$isTeacher = $user->hasRole('Teacher');
$isStudent = $user->hasRole('Student');
$isParent = $user->hasRole('Parent');
@endphp

<div x-data="{ open: false }">
    {{-- Mobile hamburger --}}
    <div class="lg:hidden fixed top-0 left-0 z-50 p-4">
        <button @click="open = !open" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    {{-- Backdrop for mobile --}}
    <div x-show="open" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-200"
         class="fixed inset-0 bg-gray-900/50 z-30 lg:hidden" @click="open = false"></div>

    {{-- Sidebar --}}
    <nav class="fixed inset-y-0 left-0 z-40 bg-white border-r border-gray-200 shadow-sm transform transition-all duration-300 ease-in-out lg:translate-x-0"
         :class="[open ? 'translate-x-0' : '-translate-x-full', collapsed ? 'w-20' : 'w-64']">

        <div class="flex flex-col h-full bg-white">
            {{-- Logo + Toggle --}}
            <div class="flex items-center h-16 px-4 border-b border-gray-200 shrink-0 gap-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <x-application-logo class="block h-8 w-auto shrink-0" style="color: #0284c7;" />
                    <span x-show="!collapsed" class="text-base font-bold bg-gradient-to-r from-sky-600 to-cyan-600 bg-clip-text text-transparent truncate whitespace-nowrap">{{ config('app.name', 'School') }}</span>
                </a>
                <button @click="$dispatch('toggle-sidebar')" class="ms-auto hidden lg:flex p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition shrink-0">
                    <i class="fa-solid" :class="collapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
                </button>
                <button @click="open = false" class="lg:hidden p-1 text-gray-400 hover:text-gray-600 ms-auto">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Nav Links --}}
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                {{-- Dashboard --}}
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :collapsed="true" label="Dashboard">
                    <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                    <span x-show="!collapsed">{{ __('Dashboard') }}</span>
                </x-nav-link>

                @if($isAdmin || $isTeacher)
                    <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')" :collapsed="true" label="Students">
                        <i class="fa-solid fa-user-graduate w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Students') }}</span>
                    </x-nav-link>
                @endif

                @if($isAdmin)
                    <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')" :collapsed="true" label="Classes">
                        <i class="fa-solid fa-school w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Classes') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')" :collapsed="true" label="Subjects">
                        <i class="fa-solid fa-book-open w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Subjects') }}</span>
                    </x-nav-link>
                @endif

                @if($isTeacher)
                    <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')" :collapsed="true" label="My Classes">
                        <i class="fa-solid fa-school w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('My Classes') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('attendances.index')" :active="request()->routeIs('attendances.*')" :collapsed="true" label="Attendance">
                        <i class="fa-solid fa-check-to-slot w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Attendance') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('exams.index')" :active="request()->routeIs('exams.*')" :collapsed="true" label="Exams">
                        <i class="fa-solid fa-pen-to-square w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Exams') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')" :collapsed="true" label="Results">
                        <i class="fa-solid fa-chart-simple w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Results') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')" :collapsed="true" label="Assignments">
                        <i class="fa-solid fa-file-pen w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Assignments') }}</span>
                    </x-nav-link>
                @endif

                @if($isStudent)
                    <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')" :collapsed="true" label="My Classes">
                        <i class="fa-solid fa-school w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('My Classes') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')" :collapsed="true" label="Homework">
                        <i class="fa-solid fa-file-pen w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Homework') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')" :collapsed="true" label="Results">
                        <i class="fa-solid fa-chart-simple w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Results') }}</span>
                    </x-nav-link>
                @endif

                @if($isParent)
                    <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')" :collapsed="true" label="Homework">
                        <i class="fa-solid fa-file-pen w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Homework') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('attendances.index')" :active="request()->routeIs('attendances.*')" :collapsed="true" label="Attendance">
                        <i class="fa-solid fa-check-to-slot w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Attendance') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')" :collapsed="true" label="Results">
                        <i class="fa-solid fa-chart-simple w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Results') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('fees.index')" :active="request()->routeIs('fees.*')" :collapsed="true" label="Fees">
                        <i class="fa-solid fa-sack-dollar w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('Fees') }}</span>
                    </x-nav-link>
                @endif

                <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')" :collapsed="true" label="Messages">
                    <i class="fa-solid fa-message w-5 text-center"></i>
                    <span x-show="!collapsed">{{ __('Messages') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" :collapsed="true" label="Notifications">
                    <i class="fa-solid fa-bell w-5 text-center"></i>
                    <span x-show="!collapsed">{{ __('Notifications') }}</span>
                </x-nav-link>

                @if($isAdmin)
                    <div class="border-t border-gray-100 my-3" x-show="!collapsed"></div>
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" :collapsed="true" label="User Management">
                        <i class="fa-solid fa-users-gear w-5 text-center"></i>
                        <span x-show="!collapsed">{{ __('User Management') }}</span>
                    </x-nav-link>
                @endif

                <div class="border-t border-gray-100 my-3" x-show="!collapsed"></div>

                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" :collapsed="true" label="Profile">
                    <i class="fa-solid fa-user w-5 text-center"></i>
                    <span x-show="!collapsed">{{ __('Profile') }}</span>
                </x-nav-link>
            </div>

            {{-- User footer --}}
            <div class="border-t border-gray-200 p-4 shrink-0">
                <div class="flex items-center gap-3 mb-3" :class="collapsed ? 'justify-center' : ''">
                    <img src="{{ $user->profile_photo_url }}" alt="" class="w-9 h-9 rounded-full shrink-0">
                    <div x-show="!collapsed" class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ $user->role }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 hover:text-gray-800 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span x-show="!collapsed">{{ __('Log Out') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
</div>
