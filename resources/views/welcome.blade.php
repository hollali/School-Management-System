<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'School Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .fade-in { animation: fadeUp 0.6s ease-out forwards; opacity: 0; }
        .fade-in-d1 { animation-delay: 0.1s; }
        .fade-in-d2 { animation-delay: 0.2s; }
        .fade-in-d3 { animation-delay: 0.3s; }
        .fade-in-d4 { animation-delay: 0.4s; }
        .fade-in-d5 { animation-delay: 0.5s; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(0,0,0,0.1); }
        .float-anim { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .hero-blob { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        .progress-ring { transition: stroke-dashoffset 0.5s ease; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

    <!-- Nav -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-b border-gray-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-700 to-blue-500 flex items-center justify-center shadow-md shadow-blue-200">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c0 1.1 2.7 2 6 2s6-.9 6-2v-5"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-base font-bold text-gray-900">{{ config('app.name', 'SchoolMS') }}</span>
                        <span class="hidden sm:inline text-xs text-gray-400 ml-2 font-medium">School Management System</span>
                    </div>
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2 transition rounded-lg hover:bg-gray-100">Sign in</a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-blue-700 rounded-lg px-5 py-2.5 hover:bg-blue-800 transition shadow-md shadow-blue-200">Get started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-b from-blue-50 via-white to-indigo-50/40 pt-16">
        <div class="absolute top-0 right-0 w-1/2 h-full opacity-[0.03]" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(37,99,235,0.1) 40px, rgba(37,99,235,0.1) 41px), repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(37,99,235,0.1) 40px, rgba(37,99,235,0.1) 41px);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <!-- Left -->
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 bg-blue-100/80 rounded-full px-4 py-1.5 text-sm font-semibold text-blue-700 fade-in border border-blue-200/50">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                        Academic Year {{ date('Y') }} — Semester 1
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-[1.1] fade-in fade-in-d1">
                        Welcome to
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 mt-1">{{ config('app.name', 'Your School') }}</span>
                    </h1>

                    <p class="text-base sm:text-lg text-gray-500 leading-relaxed max-w-lg fade-in fade-in-d2">
                        A complete platform for managing students, staff, attendance, academics, fees, and school operations — all in one place.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 fade-in fade-in-d3">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center px-7 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition shadow-xl shadow-blue-700/20 text-sm gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Register school
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-7 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-sm">
                            Sign in
                        </a>
                    </div>

                    <!-- Quick stats -->
                    <div class="grid grid-cols-3 gap-4 pt-2 fade-in fade-in-d4">
                        <div class="bg-white/70 backdrop-blur rounded-xl border border-gray-200/60 px-4 py-3">
                            <p class="text-lg font-bold text-gray-900">500+</p>
                            <p class="text-xs text-gray-400">Students</p>
                        </div>
                        <div class="bg-white/70 backdrop-blur rounded-xl border border-gray-200/60 px-4 py-3">
                            <p class="text-lg font-bold text-gray-900">40+</p>
                            <p class="text-xs text-gray-400">Teachers</p>
                        </div>
                        <div class="bg-white/70 backdrop-blur rounded-xl border border-gray-200/60 px-4 py-3">
                            <p class="text-lg font-bold text-gray-900">98%</p>
                            <p class="text-xs text-gray-400">Pass rate</p>
                        </div>
                    </div>
                </div>

                <!-- Right - Dashboard preview -->
                <div class="hidden lg:block relative fade-in fade-in-d2">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 rounded-3xl blur-2xl"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl shadow-gray-300/30 border border-gray-200/70 overflow-hidden">
                        <!-- Card header -->
                        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                            <div class="flex items-center gap-2.5">
                                <div class="flex gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                </div>
                                <span class="text-xs font-medium text-gray-400 ml-1">School Dashboard</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                Online
                            </div>
                        </div>
                        <!-- Card content -->
                        <div class="p-5 space-y-4">
                            <!-- Top row - widgets -->
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-xl p-3 border border-blue-100">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Students</span>
                                        <span class="text-xs font-bold text-blue-700">+12</span>
                                    </div>
                                    <p class="text-xl font-extrabold text-gray-900">486</p>
                                    <div class="mt-1.5 h-1.5 bg-blue-200/60 rounded-full overflow-hidden">
                                        <div class="h-full w-3/4 bg-blue-600 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-xl p-3 border border-emerald-100">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Staff</span>
                                        <span class="text-xs font-bold text-emerald-700">+2</span>
                                    </div>
                                    <p class="text-xl font-extrabold text-gray-900">38</p>
                                    <div class="mt-1.5 h-1.5 bg-emerald-200/60 rounded-full overflow-hidden">
                                        <div class="h-full w-2/3 bg-emerald-600 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-xl p-3 border border-amber-100">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Classes</span>
                                        <span class="text-xs font-bold text-amber-700">12</span>
                                    </div>
                                    <p class="text-xl font-extrabold text-gray-900">24</p>
                                    <div class="mt-1.5 h-1.5 bg-amber-200/60 rounded-full overflow-hidden">
                                        <div class="h-full w-1/2 bg-amber-600 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mid section - attendance + calendar -->
                            <div class="grid grid-cols-5 gap-3">
                                <div class="col-span-3 bg-gray-50 rounded-xl border border-gray-100 p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Weekly Attendance</span>
                                        <span class="text-[10px] text-emerald-600 font-medium">94%</span>
                                    </div>
                                    <div class="flex items-end gap-1.5 h-12">
                                        <div class="flex-1 bg-emerald-400 rounded-t-sm" style="height: 60%"></div>
                                        <div class="flex-1 bg-emerald-400 rounded-t-sm" style="height: 85%"></div>
                                        <div class="flex-1 bg-emerald-400 rounded-t-sm" style="height: 55%"></div>
                                        <div class="flex-1 bg-emerald-500 rounded-t-sm" style="height: 90%"></div>
                                        <div class="flex-1 bg-emerald-400 rounded-t-sm" style="height: 70%"></div>
                                        <div class="flex-1 bg-emerald-500 rounded-t-sm" style="height: 95%"></div>
                                        <div class="flex-1 bg-emerald-300 rounded-t-sm" style="height: 45%"></div>
                                    </div>
                                    <div class="flex justify-between mt-1.5 text-[9px] text-gray-400">
                                        <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                    </div>
                                </div>
                                <div class="col-span-2 bg-gray-50 rounded-xl border border-gray-100 p-3">
                                    <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Next Event</span>
                                    <p class="text-sm font-bold text-gray-900 mt-1">Sports Day</p>
                                    <p class="text-[10px] text-gray-400">June 15, 2026</p>
                                    <div class="mt-1 flex gap-1">
                                        <span class="text-[9px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">Upcoming</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom - notices -->
                            <div class="border-t border-gray-100 pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Recent Notices</span>
                                    <span class="text-[9px] text-blue-600 font-medium">View all</span>
                                </div>
                                <div class="space-y-1.5 max-h-16 overflow-y-auto scrollbar-thin">
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                                        <span class="text-gray-600">Mid-term exams start next Monday</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                                        <span class="text-gray-600">PTA meeting rescheduled to Friday</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                        <span class="text-gray-600">Science fair submissions due June 20</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-xs font-semibold text-blue-600 uppercase tracking-widest">School Modules</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2 mb-3">Everything your school needs</h2>
                <p class="text-gray-500 max-w-xl mx-auto">Ten integrated modules that work together seamlessly to streamline your school operations.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-blue-200 hover:shadow-lg hover:shadow-blue-100/30">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Students</h3>
                    <p class="text-xs text-gray-400 mt-1">Profiles, admissions, history</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-100/30">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Attendance</h3>
                    <p class="text-xs text-gray-400 mt-1">Daily tracking & reports</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-violet-200 hover:shadow-lg hover:shadow-violet-100/30">
                    <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-violet-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Academics</h3>
                    <p class="text-xs text-gray-400 mt-1">Grades & report cards</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-amber-200 hover:shadow-lg hover:shadow-amber-100/30">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Assignments</h3>
                    <p class="text-xs text-gray-400 mt-1">Homework & submissions</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-rose-200 hover:shadow-lg hover:shadow-rose-100/30">
                    <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Messaging</h3>
                    <p class="text-xs text-gray-400 mt-1">Parent communication</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-cyan-200 hover:shadow-lg hover:shadow-cyan-100/30">
                    <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-cyan-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 0 4.5 6h7.5a.75.75 0 0 0 .75-.75v-.75m0 0v-.75a.75.75 0 0 0-.75-.75H4.5a.75.75 0 0 0-.75.75v.75m0 0v14.25M21 6.75v.75a.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75v-.75m0 0V4.5"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Fees</h3>
                    <p class="text-xs text-gray-400 mt-1">Payments & receipts</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-purple-200 hover:shadow-lg hover:shadow-purple-100/30">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Timetable</h3>
                    <p class="text-xs text-gray-400 mt-1">Schedules & periods</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-100/30">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-indigo-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Staff</h3>
                    <p class="text-xs text-gray-400 mt-1">Teachers & admin</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-teal-200 hover:shadow-lg hover:shadow-teal-100/30">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Exams</h3>
                    <p class="text-xs text-gray-400 mt-1">Assessments & results</p>
                </div>
                <div class="bg-white rounded-xl p-5 card-hover border border-gray-100 shadow-sm hover:border-sky-200 hover:shadow-lg hover:shadow-sky-100/30">
                    <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-sky-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Library</h3>
                    <p class="text-xs text-gray-400 mt-1">Books & resources</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-xs font-semibold text-blue-600 uppercase tracking-widest">Get Started</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2 mb-3">Setup in 3 simple steps</h2>
                <p class="text-gray-500 max-w-xl mx-auto">From registration to full operation in under an hour.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <div class="hidden md:block absolute top-12 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-blue-200 via-indigo-300 to-blue-200"></div>

                <div class="relative text-center bg-white rounded-2xl p-8 shadow-sm border border-gray-100 fade-in fade-in-d1">
                    <div class="w-14 h-14 rounded-full bg-blue-700 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-200 text-lg font-bold">1</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Register your school</h3>
                    <p class="text-sm text-gray-500">Create an account and set up your school profile with basic information in seconds.</p>
                </div>
                <div class="relative text-center bg-white rounded-2xl p-8 shadow-sm border border-gray-100 fade-in fade-in-d2">
                    <div class="w-14 h-14 rounded-full bg-blue-700 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-200 text-lg font-bold">2</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Import your data</h3>
                    <p class="text-sm text-gray-500">Add students, teachers, classes, subjects, and set up your academic calendar.</p>
                </div>
                <div class="relative text-center bg-white rounded-2xl p-8 shadow-sm border border-gray-100 fade-in fade-in-d3">
                    <div class="w-14 h-14 rounded-full bg-blue-700 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-200 text-lg font-bold">3</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Go live!</h3>
                    <p class="text-sm text-gray-500">Start taking attendance, posting assignments, recording fees, and more.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-gradient-to-r from-blue-700 to-indigo-700 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.04]">
            <svg class="w-full h-full" viewBox="0 0 600 600">
                <circle cx="100" cy="100" r="60" fill="white"/>
                <circle cx="500" cy="500" r="120" fill="white"/>
                <circle cx="500" cy="100" r="40" fill="white"/>
                <circle cx="100" cy="500" r="80" fill="white"/>
            </svg>
        </div>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Ready to transform your school?</h2>
            <p class="text-lg text-blue-200 mb-10 max-w-xl mx-auto">Join schools already using our platform. Free to start — no credit card required.</p>
            <a href="{{ route('register') }}"
                class="inline-flex items-center justify-center px-10 py-4 bg-white text-blue-700 font-bold rounded-lg hover:bg-blue-50 transition shadow-2xl shadow-black/10 text-sm gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Create free account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 to-blue-500 flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c0 1.1 2.7 2 6 2s6-.9 6-2v-5"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-white">{{ config('app.name', 'SchoolMS') }}</span>
                </div>
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'School Management System') }}. All rights reserved.</p>
            </div>
            <div class="flex justify-center gap-6 mt-6 text-xs text-gray-500">
                <a href="#" class="hover:text-gray-300 transition">Privacy Policy</a>
                <a href="#" class="hover:text-gray-300 transition">Terms of Service</a>
                <a href="#" class="hover:text-gray-300 transition">Support</a>
            </div>
        </div>
    </footer>

</body>
</html>
