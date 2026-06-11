<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-9 w-auto" style="color: #0284c7;" />
                    <span class="text-lg font-bold bg-gradient-to-r from-sky-600 to-cyan-600 bg-clip-text text-transparent hidden sm:inline">{{ config('app.name', 'School') }}</span>
                </a>

                <div class="hidden sm:flex sm:items-center sm:ms-10 space-x-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                        {{ __('Students') }}
                    </x-nav-link>
                    <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                        {{ __('Classes') }}
                    </x-nav-link>
                    <x-nav-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')">
                        {{ __('Subjects') }}
                    </x-nav-link>
                    <x-nav-link :href="route('attendances.index')" :active="request()->routeIs('attendances.*')">
                        {{ __('Attendance') }}
                    </x-nav-link>
                    <x-nav-link :href="route('exams.index')" :active="request()->routeIs('exams.*')">
                        {{ __('Exams') }}
                    </x-nav-link>
                    <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                        {{ __('Homework') }}
                    </x-nav-link>
                    <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                        {{ __('Messages') }}
                    </x-nav-link>
                    <x-nav-link :href="route('fees.index')" :active="request()->routeIs('fees.*')">
                        {{ __('Fees') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-white text-xs font-bold flex items-center justify-center">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                            <svg class="ms-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                {{ __('Students') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                {{ __('Classes') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')">
                {{ __('Subjects') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('attendances.index')" :active="request()->routeIs('attendances.*')">
                {{ __('Attendance') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('exams.index')" :active="request()->routeIs('exams.*')">
                {{ __('Exams') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                {{ __('Homework') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                {{ __('Messages') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fees.index')" :active="request()->routeIs('fees.*')">
                {{ __('Fees') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-white text-sm font-bold flex items-center justify-center">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
