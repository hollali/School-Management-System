@php
$user = Auth::user();
$isAdmin = $user->hasRole('Admin');
$isTeacher = $user->hasRole('Teacher');
$isStudent = $user->hasRole('Student');
$isParent = $user->hasRole('Parent');
@endphp

<div x-data="{ open: false }" @open-mobile-sidebar.window="open = true">
    {{-- Mobile Backdrop --}}
    <div x-show="open" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-200"
         class="fixed inset-0 bg-slate-900/60 z-30 lg:hidden" @click="open = false" aria-hidden="true"></div>

    {{-- Sidebar --}}
    <nav class="fixed inset-y-0 left-0 z-40 bg-white dark:bg-slate-900 shadow-xl border-r border-gray-200 dark:border-r-0 transform transition-all duration-300 ease-in-out lg:translate-x-0"
         :class="[open ? 'translate-x-0' : '-translate-x-full', collapsed ? 'w-20' : 'w-64']"
         aria-label="Main navigation">

        <div class="flex flex-col h-full">
            {{-- Logo + Toggle --}}
            <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-slate-700/50 shrink-0 gap-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0" aria-label="Dashboard home">
                    <x-application-logo class="block h-8 w-auto shrink-0 text-gray-900 dark:text-white" />
                    <span x-show="!collapsed" class="text-base font-bold text-gray-900 dark:text-white truncate whitespace-nowrap">{{ config('app.name', 'School') }}</span>
                </a>
                <button @click="open = false" class="lg:hidden p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/10 rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400" aria-label="Close menu">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Navigation Links --}}
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
                {{-- Main Menu --}}
                <div x-show="!collapsed" class="px-3 pt-2 pb-1.5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400/80">Main Menu</p>
                </div>

                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" label="Dashboard">
                    <i class="fa-solid fa-gauge-high w-5 text-center shrink-0 text-[15px]"></i>
                    <span x-show="!collapsed" class="truncate">{{ __('Dashboard') }}</span>
                </x-nav-link>

                {{-- Academics --}}
                @if($isAdmin || $isTeacher || $isStudent)
                    <div x-data="{ open: localStorage.getItem('sidebar-academics') !== 'false' }" x-init="$watch('open', val => localStorage.setItem('sidebar-academics', val))">
                        <div x-show="!collapsed" class="pt-4 pb-1.5 px-3 cursor-pointer select-none" @click="open = !open">
                            <div class="border-t border-gray-200 dark:border-slate-700/50 mb-1.5"></div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400/80">
                                <i class="fa-solid fa-chevron-down mr-1 transition-transform duration-200" :class="{ '-rotate-90': !open }"></i>
                                Academics
                            </p>
                        </div>
                        <div x-show="open">

                    @if($isAdmin || $isTeacher)
                        <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')" label="Students">
                            <i class="fa-solid fa-user-graduate w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Students') }}</span>
                        </x-nav-link>
                    @endif

                    @if($isAdmin)
                        <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')" label="Classes">
                            <i class="fa-solid fa-school w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Classes') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')" label="Subjects">
                            <i class="fa-solid fa-book-open w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Subjects') }}</span>
                        </x-nav-link>
                    @endif

                    @if($isTeacher)
                        <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')" label="My Classes">
                            <i class="fa-solid fa-school w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('My Classes') }}</span>
                        </x-nav-link>
                    @endif

                    @if($isStudent)
                        <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')" label="My Classes">
                            <i class="fa-solid fa-school w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('My Classes') }}</span>
                        </x-nav-link>
                    @endif
                        </div>
                    </div>
                @endif

                {{-- Assessment --}}
                @if($isTeacher || $isStudent || $isParent)
                    <div x-data="{ open: localStorage.getItem('sidebar-assessment') !== 'false' }" x-init="$watch('open', val => localStorage.setItem('sidebar-assessment', val))">
                        <div x-show="!collapsed" class="pt-4 pb-1.5 px-3 cursor-pointer select-none" @click="open = !open">
                            <div class="border-t border-gray-200 dark:border-slate-700/50 mb-1.5"></div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400/80">
                                <i class="fa-solid fa-chevron-down mr-1 transition-transform duration-200" :class="{ '-rotate-90': !open }"></i>
                                Assessment
                            </p>
                        </div>
                        <div x-show="open">

                    @if($isTeacher)
                        <x-nav-link :href="route('attendance.dashboard')" :active="request()->routeIs('attendance.*')" label="Attendance">
                            <i class="fa-solid fa-check-to-slot w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Attendance') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('exams.index')" :active="request()->routeIs('exams.*') && !request()->routeIs('question-bank.*', 'exam-schedules.*', 'grading.*', 'exam-reports.*')" label="Exams">
                            <i class="fa-solid fa-pen-to-square w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Exams') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.*')" label="Question Bank">
                            <i class="fa-solid fa-circle-question w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Question Bank') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('exam-schedules.index')" :active="request()->routeIs('exam-schedules.*')" label="Schedules">
                            <i class="fa-solid fa-calendar-days w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Schedules') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('grading.index')" :active="request()->routeIs('grading.*')" label="Grading">
                            <i class="fa-solid fa-check-double w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Grading') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('exam-reports.index')" :active="request()->routeIs('exam-reports.*')" label="Reports">
                            <i class="fa-solid fa-chart-column w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Reports') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')" label="Results">
                            <i class="fa-solid fa-chart-simple w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Results') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')" label="Assignments">
                            <i class="fa-solid fa-file-pen w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Assignments') }}</span>
                        </x-nav-link>
                    @endif

                    @if($isStudent)
                        <x-nav-link :href="route('attendance.student.show')" :active="request()->routeIs('attendance.*')" label="Attendance">
                            <i class="fa-solid fa-check-to-slot w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Attendance') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('student.exams')" :active="request()->routeIs('student.exams*') && !request()->routeIs('student.exams.history*')" label="My Exams">
                            <i class="fa-solid fa-pen-to-square w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('My Exams') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('student.exams.history')" :active="request()->routeIs('student.exams.history*')" label="Exam History">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Exam History') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')" label="Homework">
                            <i class="fa-solid fa-file-pen w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Homework') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')" label="Results">
                            <i class="fa-solid fa-chart-simple w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Results') }}</span>
                        </x-nav-link>
                    @endif

                    @if($isParent)
                        <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')" label="Homework">
                            <i class="fa-solid fa-file-pen w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Homework') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('attendance.student.show')" :active="request()->routeIs('attendance.*')" label="Attendance">
                            <i class="fa-solid fa-check-to-slot w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Attendance') }}</span>
                        </x-nav-link>

                        <x-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')" label="Results">
                            <i class="fa-solid fa-chart-simple w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Results') }}</span>
                        </x-nav-link>
                    @endif
                        </div>
                    </div>
                @endif

                {{-- Communication --}}
                <div x-data="{ open: localStorage.getItem('sidebar-communication') !== 'false' }" x-init="$watch('open', val => localStorage.setItem('sidebar-communication', val))">
                    <div x-show="!collapsed" class="pt-4 pb-1.5 px-3 cursor-pointer select-none" @click="open = !open">
                        <div class="border-t border-gray-200 dark:border-slate-700/50 mb-1.5"></div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400/80">
                            <i class="fa-solid fa-chevron-down mr-1 transition-transform duration-200" :class="{ '-rotate-90': !open }"></i>
                            Communication
                        </p>
                    </div>
                    <div x-show="open">
                        <div class="relative"
                            x-data="{ unreadCount: 0 }"
                            x-init="
                                const refresh = async () => {
                                    try {
                                        const res = await fetch('{{ route('conversations.unread.total') }}');
                                        const data = await res.json();
                                        unreadCount = data.count;
                                    } catch(e) {}
                                };
                                refresh();
                                document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
                                window.addEventListener('focus', refresh);
                                if (window.Echo) {
                                    Echo.channel('notifications.{{ auth()->id() }}')
                                        .listen('.notification.received', (e) => {
                                            if (e.type === 'message') unreadCount++;
                                        });
                                }
                            ">
                            <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')" label="Messages">
                                <i class="fa-solid fa-message w-5 text-center shrink-0 text-[15px]"></i>
                                <span x-show="!collapsed" class="truncate">{{ __('Messages') }}</span>
                            </x-nav-link>
                            <span x-show="unreadCount > 0"
                                class="absolute top-0.5 left-4 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-sky-500 rounded-full"
                                x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                        </div>

                        <x-nav-link :href="route('announcements.index')" :active="request()->routeIs('announcements.*')" label="Announcements">
                            <i class="fa-solid fa-bullhorn w-5 text-center shrink-0 text-[15px]"></i>
                            <span x-show="!collapsed" class="truncate">{{ __('Announcements') }}</span>
                        </x-nav-link>

                        <div class="relative"
                            x-data="{ unreadCount: {{ \App\Models\AppNotification::forUser(auth()->user())->unread()->count() }} }"
                            x-init="
                                if (window.Echo) {
                                    Echo.channel('notifications.{{ auth()->id() }}')
                                        .listen('.notification.received', () => { unreadCount++; });
                                }
                            ">
                            <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" label="Notifications">
                                <i class="fa-solid fa-bell w-5 text-center shrink-0 text-[15px]"></i>
                                <span x-show="!collapsed" class="truncate">{{ __('Notifications') }}</span>
                            </x-nav-link>
                            <span x-show="unreadCount > 0"
                                class="absolute top-0.5 left-4 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full"
                                x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                        </div>
                    </div>
                </div>

                {{-- Administration --}}
                @if($isAdmin)
                    <div x-data="{ open: localStorage.getItem('sidebar-administration') !== 'false' }" x-init="$watch('open', val => localStorage.setItem('sidebar-administration', val))">
                        <div x-show="!collapsed" class="pt-4 pb-1.5 px-3 cursor-pointer select-none" @click="open = !open">
                            <div class="border-t border-gray-200 dark:border-slate-700/50 mb-1.5"></div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400/80">
                                <i class="fa-solid fa-chevron-down mr-1 transition-transform duration-200" :class="{ '-rotate-90': !open }"></i>
                                Administration
                            </p>
                        </div>
                        <div x-show="open">

                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" label="User Management">
                        <i class="fa-solid fa-users-gear w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('User Management') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('admin.class-assignments')" :active="request()->routeIs('admin.class-assignments*')" label="Class Assignments">
                        <i class="fa-solid fa-people-arrows w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Class Assignments') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('attendance.dashboard')" :active="request()->routeIs('attendance.*')" label="Attendance">
                        <i class="fa-solid fa-check-to-slot w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Attendance') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('staff-attendance.index')" :active="request()->routeIs('staff-attendance.*')" label="Staff Attendance">
                        <i class="fa-solid fa-clipboard-user w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Staff Attendance') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('holidays.index')" :active="request()->routeIs('holidays.*')" label="Holidays">
                        <i class="fa-solid fa-calendar-xmark w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Holidays') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.*')" label="Question Bank">
                        <i class="fa-solid fa-circle-question w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Question Bank') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('exam-schedules.index')" :active="request()->routeIs('exam-schedules.*')" label="Exam Schedules">
                        <i class="fa-solid fa-calendar-days w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Exam Schedules') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('exam-reports.index')" :active="request()->routeIs('exam-reports.*')" label="Exam Reports">
                        <i class="fa-solid fa-chart-column w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Exam Reports') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('academic-terms.index')" :active="request()->routeIs('academic-terms.*')" label="Academic Terms">
                        <i class="fa-solid fa-calendar-week w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Academic Terms') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('finance.dashboard')" :active="request()->routeIs('finance.*')" label="Finance">
                        <i class="fa-solid fa-sack-dollar w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Finance') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('fees.index')" :active="request()->routeIs('fees.*')" label="Invoices">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Invoices') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')" label="Payments">
                        <i class="fa-solid fa-credit-card w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Payments') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('receipts.index')" :active="request()->routeIs('receipts.*')" label="Receipts">
                        <i class="fa-solid fa-receipt w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Receipts') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('fee-structures.index')" :active="request()->routeIs('fee-structures.*')" label="Fee Structures">
                        <i class="fa-solid fa-layer-group w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Fee Structures') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('fee-categories.index')" :active="request()->routeIs('fee-categories.*')" label="Fee Categories">
                        <i class="fa-solid fa-tags w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Fee Categories') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('discounts.index')" :active="request()->routeIs('discounts.*')" label="Discounts">
                        <i class="fa-solid fa-percent w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Discounts') }}</span>
                    </x-nav-link>
                        </div>
                    </div>
                @endif

                {{-- Finance --}}
                @if($isStudent || $isParent)
                    <div x-data="{ open: localStorage.getItem('sidebar-finance') !== 'false' }" x-init="$watch('open', val => localStorage.setItem('sidebar-finance', val))">
                        <div x-show="!collapsed" class="pt-4 pb-1.5 px-3 cursor-pointer select-none" @click="open = !open">
                            <div class="border-t border-gray-200 dark:border-slate-700/50 mb-1.5"></div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400/80">
                                <i class="fa-solid fa-chevron-down mr-1 transition-transform duration-200" :class="{ '-rotate-90': !open }"></i>
                                Finance
                            </p>
                        </div>
                        <div x-show="open">

                    @if($isStudent)
                    <x-nav-link :href="route('finance.dashboard')" :active="request()->routeIs('finance.*')" label="Finance">
                        <i class="fa-solid fa-sack-dollar w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Finance') }}</span>
                    </x-nav-link>
                    @endif

                    <x-nav-link :href="route('fees.index')" :active="request()->routeIs('fees.*')" label="Invoices">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Invoices') }}</span>
                    </x-nav-link>

                    @if($isParent)
                    <x-nav-link :href="route('payments.parent.history')" :active="request()->routeIs('payments.parent.*')" label="My Payments">
                        <i class="fa-solid fa-credit-card w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('My Payments') }}</span>
                    </x-nav-link>
                    @endif

                    <x-nav-link :href="route('receipts.index')" :active="request()->routeIs('receipts.*')" label="Receipts">
                        <i class="fa-solid fa-receipt w-5 text-center shrink-0 text-[15px]"></i>
                        <span x-show="!collapsed" class="truncate">{{ __('Receipts') }}</span>
                    </x-nav-link>
                        </div>
                    </div>
                @endif
            </div>

            {{-- User Footer --}}
            <div class="border-t border-gray-200 dark:border-slate-700/50 p-4 shrink-0">
                <div class="flex items-center gap-3 mb-3" :class="collapsed ? 'justify-center' : ''">
                    <img src="{{ $user->profile_photo_url }}" alt="" class="w-9 h-9 rounded-full shrink-0 ring-2 ring-gray-300 dark:ring-slate-600">
                    <div x-show="!collapsed" class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $user->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $user->role }}</div>
                    </div>
                </div>

                <button @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark', darkMode); localStorage.setItem('darkMode', darkMode)"
                    class="w-full flex items-center gap-2 px-3 py-2.5 mb-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 dark:text-slate-300 dark:bg-white/5 dark:hover:bg-white/10 dark:hover:text-white rounded-lg transition-all duration-200 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-900"
                    :class="collapsed ? 'justify-center' : ''">
                    <template x-if="darkMode">
                        <i class="fa-solid fa-sun"></i>
                    </template>
                    <template x-if="!darkMode">
                        <i class="fa-solid fa-moon"></i>
                    </template>
                    <span x-show="!collapsed" x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 dark:text-slate-300 dark:bg-white/5 dark:hover:bg-white/10 dark:hover:text-white rounded-lg transition-all duration-200 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-900" :class="collapsed ? 'justify-center' : ''">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span x-show="!collapsed">{{ __('Log Out') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
</div>
