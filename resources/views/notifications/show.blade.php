<x-app-layout>
    @section('title', __('Notification'))

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ __('Notification') }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Notification details</p>
            </div>
            <a href="{{ route('notifications.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8">
                <p class="text-xs text-gray-400 dark:text-slate-500 mb-2">{{ $notification->created_at->format('F d, Y \a\t h:i A') }}</p>
                <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200 mb-4">
                    {{ $notification->data['title'] ?? $notification->type }}
                </h3>
                <div class="prose prose-sm max-w-none text-gray-600 dark:text-slate-400">
                    {!! nl2br(e($notification->data['body'] ?? '')) !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
