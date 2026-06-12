<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Notification') }}</h2>
            <a href="{{ route('notifications.index') }}"
                class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8">
                <p class="text-xs text-gray-400 mb-2">{{ $notification->created_at->format('F d, Y \a\t h:i A') }}</p>
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    {{ $notification->data['title'] ?? $notification->type }}
                </h3>
                <div class="prose prose-sm max-w-none text-gray-600">
                    {!! nl2br(e($notification->data['body'] ?? '')) !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
