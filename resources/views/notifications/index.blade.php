<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Notifications') }}</h2>
            <div class="flex gap-2">
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                        {{ __('Mark All Read') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden">
                @forelse($notifications as $notification)
                    <a href="{{ route('notifications.show', $notification) }}"
                       class="block px-6 py-4 border-b border-gray-100 hover:bg-gray-50/50 transition {{ is_null($notification->read_at) ? 'bg-sky-50/50' : '' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold {{ is_null($notification->read_at) ? 'text-gray-900' : 'text-gray-600' }}">
                                    {{ $notification->data['title'] ?? $notification->type }}
                                </p>
                                <p class="text-sm text-gray-500 mt-0.5 truncate">
                                    {{ $notification->data['body'] ?? '' }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-400 whitespace-nowrap shrink-0">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <i class="fa-solid fa-bell text-4xl text-gray-200 mb-4"></i>
                        <p class="text-sm text-gray-500">No notifications yet.</p>
                    </div>
                @endforelse

                <div class="p-4 border-t border-gray-100">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
