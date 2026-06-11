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
    <nav class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 shadow-sm transform transition-transform duration-300 ease-in-out lg:translate-x-0"
         :class="open ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex flex-col h-full bg-white">
            {{-- Logo --}}
            <div class="flex items-center justify-between h-16 px-5 border-b border-gray-200 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <x-application-logo class="block h-8 w-auto" style="color: #0284c7;" />
                    <span class="text-base font-bold bg-gradient-to-r from-sky-600 to-cyan-600 bg-clip-text text-transparent">{{ config('app.name', 'School') }}</span>
                </a>
                <button @click="open = false" class="lg:hidden p-1 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Nav Links --}}
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                    <span>{{ __('Dashboard') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                    <i class="fa-solid fa-user-graduate w-5 text-center"></i>
                    <span>{{ __('Students') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                    <i class="fa-solid fa-school w-5 text-center"></i>
                    <span>{{ __('Classes') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')">
                    <i class="fa-solid fa-book-open w-5 text-center"></i>
                    <span>{{ __('Subjects') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('attendances.index')" :active="request()->routeIs('attendances.*')">
                    <i class="fa-solid fa-check-to-slot w-5 text-center"></i>
                    <span>{{ __('Attendance') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('exams.index')" :active="request()->routeIs('exams.*')">
                    <i class="fa-solid fa-pen-to-square w-5 text-center"></i>
                    <span>{{ __('Exams') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                    <i class="fa-solid fa-file-pen w-5 text-center"></i>
                    <span>{{ __('Homework') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                    <i class="fa-solid fa-message w-5 text-center"></i>
                    <span>{{ __('Messages') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('fees.index')" :active="request()->routeIs('fees.*')">
                    <i class="fa-solid fa-sack-dollar w-5 text-center"></i>
                    <span>{{ __('Fees') }}</span>
                </x-nav-link>

                <div class="border-t border-gray-100 my-3"></div>

                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                    <i class="fa-solid fa-user w-5 text-center"></i>
                    <span>{{ __('Profile') }}</span>
                </x-nav-link>
            </div>

            {{-- User footer --}}
            <div class="border-t border-gray-200 p-4 shrink-0">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-white text-sm font-bold flex items-center justify-center shrink-0">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 hover:text-gray-800 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </nav>
</div>
