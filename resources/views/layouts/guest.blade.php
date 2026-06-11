<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'School Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-sky-700 via-sky-600 to-cyan-600 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" fill="white" />
                    <circle cx="700" cy="200" r="120" fill="white" />
                    <circle cx="200" cy="600" r="100" fill="white" />
                    <circle cx="650" cy="650" r="150" fill="white" />
                    <circle cx="400" cy="400" r="200" fill="white" />
                    <rect x="50" y="400" width="80" height="80" rx="10" fill="white" />
                    <rect x="600" cy="50" width="60" height="60" rx="8" fill="white" />
                </svg>
            </div>
            <div class="relative flex flex-col justify-center px-16">
                <div class="mb-8">
                    <svg class="w-16 h-16 text-white" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="60" height="60" rx="12" fill="white" fill-opacity="0.2"/>
                        <path d="M30 12L14 24v16a4 4 0 004 4h24a4 4 0 004-4V24L30 12z" fill="white" stroke="white" stroke-width="1.5"/>
                        <path d="M24 36h12M24 30h12M24 24h8" stroke="#0369a1" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    School Management<br>System
                </h1>
                <p class="text-lg text-sky-100 leading-relaxed">
                    Streamlining education management with powerful tools for administrators, teachers, students, and parents.
                </p>
                <div class="mt-12 grid grid-cols-2 gap-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <p class="text-2xl font-bold text-white">500+</p>
                        <p class="text-sm text-sky-200">Students Enrolled</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <p class="text-2xl font-bold text-white">98%</p>
                        <p class="text-sm text-sky-200">Attendance Rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Form -->
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-gray-50">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex justify-center mb-8">
                    <div class="bg-gradient-to-br from-sky-600 to-cyan-600 p-3 rounded-2xl shadow-lg">
                        <svg class="w-10 h-10 text-white" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M30 12L14 24v16a4 4 0 004 4h24a4 4 0 004-4V24L30 12z" fill="white" stroke="white" stroke-width="1.5"/>
                            <path d="M24 36h12M24 30h12M24 24h8" stroke="#0369a1" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8 sm:p-10">
                    {{ $slot }}
                </div>

                <p class="text-center text-sm text-gray-400 mt-8">
                    &copy; {{ date('Y') }} School Management System. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
