<x-app-layout>
    @section('title', 'Conversations')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Conversations</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">View and manage your conversations</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Conversations" :data="$conversations" searchable="true" searchPlaceholder="Search conversations..." searchValue="{{ request('search') }}" searchRoute="{{ route('conversations.index') }}">
            <x-slot name="actions">
                @unless(Auth::user()->hasRole('Parent'))
                    <button @click="$dispatch('open-modal', 'create-conversation')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                        New Conversation
                    </button>
                @endunless
            </x-slot>
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Participants</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Last Message</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Last Activity</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($conversations as $conversation)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('conversations.show', $conversation) }}" class="text-sky-600 hover:text-sky-800 font-medium">
                                {{ $conversation->subject }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $conversation->creator->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300 max-w-xs truncate">
                            {{ $conversation->messages->last()?->body ? Str::limit($conversation->messages->last()->body, 50) : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">
                            {{ $conversation->messages->last()?->created_at?->format('M d, Y H:i') ?? $conversation->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="
                                $dispatch('view-conversation', {
                                    subject: '{{ $conversation->subject }}',
                                    creator: '{{ $conversation->creator->name }}',
                                    created_at: '{{ $conversation->created_at->format('M d, Y') }}',
                                    message_count: '{{ $conversation->messages->count() }}',
                                    last_message: '{{ $conversation->messages->last()?->body ? Str::limit($conversation->messages->last()->body, 100) : '—' }}',
                                    show_url: '{{ route('conversations.show', $conversation) }}'
                                });
                                $dispatch('open-modal', 'view-conversation');
                            " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="Quick View">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @unless(Auth::user()->hasRole('Parent'))
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('conversations.destroy', $conversation) }}',
                                    method: 'DELETE',
                                    title: 'Delete Conversation',
                                    message: 'Delete this conversation? This action cannot be undone.',
                                    confirmLabel: 'Delete',
                                    confirmClass: 'bg-red-600 hover:bg-red-700'
                                })" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">No conversations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-conversation" maxWidth="lg" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Conversation') }}</h2>
                <button @click="$dispatch('close-modal', 'create-conversation')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('conversations.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"
                            required>
                        @error('subject')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Message</label>
                        <textarea name="message" id="message" rows="4"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"
                            required>{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center justify-end gap-4">
                        <button @click="$dispatch('close-modal', 'create-conversation')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            {{ __('Create Conversation') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-conversation" maxWidth="xl" focusable>
        <div class="p-6" x-data="{ conversation: null }" @view-conversation.window="conversation = $event.detail">
            <template x-if="conversation">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="conversation.subject"></h2>
                        <button @click="$dispatch('close-modal', 'view-conversation')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Created By</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="conversation.creator"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Messages</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="conversation.message_count"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Created At</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="conversation.created_at"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Last Message</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="conversation.last_message"></dd>
                        </div>
                    </dl>
                    <div class="mt-6 flex justify-end">
                        <a :href="conversation.show_url"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            {{ __('View Full Conversation') }}
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </x-modal>
</x-app-layout>
