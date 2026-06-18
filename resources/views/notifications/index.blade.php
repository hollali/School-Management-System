<x-app-layout>
    @section('title', __('Notifications'))

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ __('Notifications') }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Your notification center</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                        <i class="fa-solid fa-check-double mr-2"></i>
                        {{ __('Mark All Read') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter Tabs --}}
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <a href="{{ route('notifications.index') }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition
                        {{ !request('type') && !request('status') ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    All ({{ $counts['all'] }})
                </a>
                <a href="{{ route('notifications.index', ['type' => 'assignment']) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition
                        {{ request('type') === 'assignment' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    <i class="fa-solid fa-file-pen mr-1.5"></i>
                    Assignments ({{ $counts['assignment'] }})
                </a>
                <a href="{{ route('notifications.index', ['type' => 'submission']) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition
                        {{ request('type') === 'submission' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    <i class="fa-solid fa-upload mr-1.5"></i>
                    Submissions ({{ $counts['submission'] }})
                </a>
                <a href="{{ route('notifications.index', ['type' => 'grade']) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition
                        {{ request('type') === 'grade' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    <i class="fa-solid fa-chart-simple mr-1.5"></i>
                    Grades ({{ $counts['grade'] }})
                </a>
                <a href="{{ route('notifications.index', ['type' => 'message']) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition
                        {{ request('type') === 'message' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    <i class="fa-solid fa-message mr-1.5"></i>
                    Messages ({{ $counts['message'] }})
                </a>
                <a href="{{ route('notifications.index', ['type' => 'announcement']) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition
                        {{ request('type') === 'announcement' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    <i class="fa-solid fa-bullhorn mr-1.5"></i>
                    Announcements ({{ $counts['announcement'] }})
                </a>
                <a href="{{ route('notifications.index', ['status' => 'unread']) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition
                        {{ request('status') === 'unread' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    <i class="fa-solid fa-circle mr-1.5"></i>
                    Unread ({{ $counts['unread'] }})
                </a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                @forelse($notifications as $notification)
                    @php $notifData = $notification->data; @endphp
                    <a href="{{ route('notifications.show', $notification) }}"
                       class="block px-6 py-4 border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 transition {{ $notification->isUnread() ? 'bg-sky-50 dark:bg-sky-900/10' : '' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                @if($notification->isUnread())
                                    <span class="w-2 h-2 mt-2 shrink-0 rounded-full bg-sky-500"></span>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        @php
                                            $typeIcons = [
                                                'assignment' => 'fa-solid fa-file-pen text-blue-500',
                                                'submission' => 'fa-solid fa-upload text-green-500',
                                                'grade' => 'fa-solid fa-chart-simple text-purple-500',
                                                'message' => 'fa-solid fa-message text-amber-500',
                                                'announcement' => 'fa-solid fa-bullhorn text-red-500',
                                                'system' => 'fa-solid fa-gear text-gray-500',
                                            ];
                                            $icon = $typeIcons[$notification->type] ?? 'fa-solid fa-bell text-gray-400';
                                        @endphp
                                        <i class="{{ $icon }} text-xs"></i>
                                        <p class="text-sm font-semibold {{ $notification->isUnread() ? 'text-gray-900 dark:text-slate-100' : 'text-gray-600 dark:text-slate-400' }}">
                                            {{ $notifData['title'] ?? $notification->type }}
                                            @if(!empty($notifData['updated']))
                                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">updated</span>
                                            @endif
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5 line-clamp-2">
                                        {{ $notifData['body'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 dark:text-slate-500 whitespace-nowrap shrink-0">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <i class="fa-solid fa-bell-slash text-4xl text-gray-200 dark:text-slate-600 mb-4"></i>
                        <p class="text-sm text-gray-500 dark:text-slate-400">
                            @if(request('type'))
                                No {{ request('type') }} notifications.
                            @elseif(request('status') === 'unread')
                                No unread notifications.
                            @else
                                No notifications yet.
                            @endif
                        </p>
                    </div>
                @endforelse

                <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $notifications->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
