@php
    $isOwn = $message->sender_id === auth()->id();
    $isSystem = $message->type === 'system';
    $currentUser = auth()->user();
    $isAdmin = $currentUser->isAdmin();
    $isConversationCreator = $message->conversation->created_by === $currentUser->id;
    $canDelete = $isOwn || $isAdmin || $isConversationCreator;
@endphp
<div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} mb-3 group"
     x-data="{ showActions: false }"
     id="message-{{ $message->id }}"
     data-message-id="{{ $message->id }}"
     data-sender-id="{{ $message->sender_id }}">
    <div class="max-w-[75%] {{ $isOwn ? 'order-1' : 'order-1' }}">
        @if(!$isOwn && !$isSystem)
        <div class="flex items-center gap-2 mb-1 px-1">
            <img src="{{ $message->sender->profile_photo_url }}" alt="" class="w-5 h-5 rounded-full object-cover flex-shrink-0">
            <span class="text-xs font-medium text-gray-600 dark:text-slate-400">{{ $message->sender->name }}</span>
        </div>
        @endif

        @if($message->parent_id)
        <div class="mb-1 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-slate-700/50 border-l-2 border-sky-400 text-xs text-gray-500 dark:text-slate-400 cursor-pointer"
             @click="document.getElementById('message-{{ $message->parent_id }}')?.scrollIntoView({ behavior: 'smooth', block: 'center' })">
            @php $parent = $message->parent; @endphp
            @if($parent)
                <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $parent->sender->name }}</span>: {{ Str::limit($parent->body, 80) }}
            @else
                <span class="italic text-gray-400 dark:text-slate-500">Reply to a deleted message</span>
            @endif
        </div>
        @endif

        @if($message->forwarded_from)
        <div class="mb-1 px-3 py-1 rounded-lg bg-gray-50 dark:bg-slate-800/50 text-xs text-gray-400 dark:text-slate-500">
            <i class="fa-solid fa-forward mr-1"></i> Forwarded
        </div>
        @endif

        @if($isSystem)
        <div class="text-center text-xs text-gray-400 dark:text-slate-500 py-2 italic">
            {{ $message->body }}
        </div>
        @elseif($message->trashed())
        <div class="px-4 py-2 rounded-2xl bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-500 italic text-sm">
            <i class="fa-regular fa-trash-can mr-1"></i> This message was deleted
        </div>
        @elseif($message->type === 'file')
        <div class="px-4 py-3 rounded-2xl {{ $isOwn ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white' }} shadow-sm border {{ $isOwn ? 'border-sky-600' : 'border-gray-200 dark:border-slate-600' }}">
            @if($message->body)
            <p class="mb-2 text-sm whitespace-pre-wrap break-words">{{ $message->body }}</p>
            @endif
            <a href="{{ $message->file_url }}" target="_blank"
               class="flex items-center gap-3 p-3 rounded-xl {{ $isOwn ? 'bg-sky-600 hover:bg-sky-700' : 'bg-gray-50 dark:bg-slate-600 hover:bg-gray-100 dark:hover:bg-slate-500' }} transition">
                <i class="{{ $message->file_icon }} text-xl"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate {{ $isOwn ? 'text-white' : 'text-gray-900 dark:text-white' }}">{{ $message->file_name }}</p>
                    <p class="text-xs {{ $isOwn ? 'text-sky-200' : 'text-gray-500 dark:text-slate-400' }}">
                        @if($message->file_size > 1048576)
                            {{ round($message->file_size / 1048576, 1) }} MB
                        @else
                            {{ round($message->file_size / 1024, 0) }} KB
                        @endif
                    </p>
                </div>
                <i class="fa-solid fa-download {{ $isOwn ? 'text-white' : 'text-gray-400 dark:text-slate-400' }}"></i>
            </a>
        </div>
        @else
        <div class="px-4 py-2.5 rounded-2xl {{ $isOwn ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white' }} shadow-sm border {{ $isOwn ? 'border-sky-600' : 'border-gray-200 dark:border-slate-600' }}">
            <p class="text-sm whitespace-pre-wrap break-words">{{ $message->body }}</p>
        </div>
        @endif

        <div class="flex items-center gap-2 mt-0.5 {{ $isOwn ? 'justify-end' : 'justify-start' }} px-1">
            <span class="text-[10px] {{ $isOwn ? 'text-gray-400' : 'text-gray-400 dark:text-slate-500' }}">
                {{ $message->created_at->format('g:i A') }}
                @if($message->edited_at)
                    <span class="italic">(edited)</span>
                @endif
            </span>

            @if($message->reactions_count > 0)
            <div class="flex items-center gap-0.5">
                @foreach($message->reactions->groupBy('reaction') as $reaction => $reacts)
                <span class="inline-flex items-center gap-0.5 text-xs bg-gray-100 dark:bg-slate-600 rounded-full px-1.5 py-0.5 cursor-pointer"
                      @click="toggleReaction({{ $message->id }}, '{{ $reaction }}')">
                    {{ $reaction }}<span class="font-medium text-gray-500 dark:text-slate-400">{{ $reacts->count() }}</span>
                </span>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @if(!$isSystem && !$message->trashed())
    <div class="flex items-center gap-0.5 {{ $isOwn ? 'order-2 ml-1' : 'order-0 mr-1' }} opacity-0 group-hover:opacity-100 transition-opacity">
        @if(!$isOwn)
        <button @click="replyToMessage({{ $message->id }})" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition" title="Reply">
            <i class="fa-solid fa-reply text-xs"></i>
        </button>
        @endif
        <button @click="toggleReaction({{ $message->id }}, '\u2764\ufe0f')" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 hover:text-red-400 transition" title="React with heart">
            <i class="fa-regular fa-heart text-xs"></i>
        </button>
        @if($isOwn)
        <button @click="editMessage({{ $message->id }})" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition" title="Edit">
            <i class="fa-regular fa-pen-to-square text-xs"></i>
        </button>
        @endif
        <button @click="showActions = !showActions"
                class="p-1 rounded hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition"
                title="More">
            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
        </button>
        <div x-show="showActions" @click.outside="showActions = false"
             class="absolute bottom-full right-0 mb-1 w-40 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-1 z-50 text-sm"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <button @click="replyToMessage({{ $message->id }}); showActions = false" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-700 dark:text-slate-300">
                <i class="fa-solid fa-reply text-xs"></i> Reply
            </button>
            <button @click="showForwardModal({{ $message->id }}); showActions = false" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-700 dark:text-slate-300">
                <i class="fa-solid fa-forward text-xs"></i> Forward
            </button>
            @if($isOwn)
            <button @click="editMessage({{ $message->id }}); showActions = false" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-700 dark:text-slate-300">
                <i class="fa-regular fa-pen-to-square text-xs"></i> Edit
            </button>
            @endif
            @if($canDelete)
            <button @click="deleteMessage({{ $message->id }}); showActions = false" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400">
                <i class="fa-regular fa-trash-can text-xs"></i> Delete
            </button>
            @endif
        </div>
    </div>
    @endif
</div>
