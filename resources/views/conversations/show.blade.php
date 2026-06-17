<x-app-layout>
    @section('title', $conversation->subject)

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ $conversation->subject }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Conversation thread</p>
            </div>
            <a href="{{ route('conversations.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Back to list') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 max-w-3xl mx-auto space-y-6 max-h-[600px] overflow-y-auto">
                    @forelse($conversation->messages as $message)
                        @if($message->sender_id === auth()->id())
                            <div class="flex justify-end">
                                <div class="bg-gradient-to-r from-sky-500 to-sky-600 text-white rounded-2xl rounded-br-sm px-5 py-3 max-w-md ml-auto shadow-sm">
                                    <div class="text-xs font-semibold uppercase tracking-wide opacity-75">{{ $message->sender->name }}</div>
                                    <p class="mt-1 text-sm">{{ $message->body }}</p>
                                    <div class="mt-1 text-xs opacity-75">{{ $message->created_at->format('M d, Y g:i A') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="bg-gray-100 dark:bg-slate-700/50 text-gray-800 dark:text-slate-200 rounded-2xl rounded-bl-sm px-5 py-3 max-w-md shadow-sm">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">{{ $message->sender->name }}</div>
                                    <p class="mt-1 text-sm">{{ $message->body }}</p>
                                    <div class="mt-1 text-xs text-gray-400 dark:text-slate-500">{{ $message->created_at->format('M d, Y g:i A') }}</div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="text-center text-sm text-gray-600 dark:text-slate-400">No messages yet.</p>
                    @endforelse
                </div>

                <div class="border-t border-gray-100 dark:border-slate-700 p-6">
                    <form action="{{ route('conversations.message', $conversation) }}" method="POST">
                        @csrf
                        <div class="flex gap-4">
                            <textarea name="message" rows="2" placeholder="Type your reply..."
                                class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700"
                                required>{{ old('message') }}</textarea>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm shrink-0">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                {{ __('Send') }}
                            </button>
                        </div>
                        @error('message')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
