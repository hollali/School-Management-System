<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'School Management System'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>if(localStorage.getItem('darkMode')==='true')document.documentElement.classList.add('dark')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @@media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
            table { font-size: 12px; }
            th { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white dark:bg-slate-900">
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 lg:flex"
         x-data="{ collapsed: false }"
         x-init="try { collapsed = localStorage.getItem('sidebarCollapsed') === 'true' } catch(e) { collapsed = false }"
         @toggle-sidebar.window="collapsed = !collapsed; try { localStorage.setItem('sidebarCollapsed', collapsed) } catch(e) {}">

        @include('layouts.navigation')

        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
             :class="{'lg:ml-20': collapsed, 'lg:ml-64': !collapsed}">

            {{-- Top Header Bar --}}
            <header class="sticky top-0 z-30 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700/50 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <button @click="$dispatch('open-mobile-sidebar')" class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/10 rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400">
                            <i class="fa-regular fa-bell text-xl"></i>
                            @php $headerUnread = \App\Models\AppNotification::forUser(auth()->user())->unread()->count(); @endphp
                            @if($headerUnread > 0)
                                <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">{{ $headerUnread > 9 ? '9+' : $headerUnread }}</span>
                            @endif
                        </a>

                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-slate-300">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 dark:text-slate-500 transition-transform" :class="{'rotate-180': userMenuOpen}"></i>
                            </button>

                            <div x-show="userMenuOpen" @click.outside="userMenuOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <i class="fa-regular fa-user w-4 text-center text-gray-400 dark:text-slate-500"></i>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-center text-gray-400 dark:text-slate-500"></i>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            @isset($header)
                <div class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700/50">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <x-toast />
    <x-confirmation-modal />

    @stack('scripts')
</body>
</html>
