<x-app-layout>
    @section('title', 'Messages')

    @push('styles')
<style>
    .chat-container { height: calc(100vh - 8rem); }
    .messages-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
    .messages-scroll::-webkit-scrollbar { width: 4px; }
    .messages-scroll::-webkit-scrollbar-track { background: transparent; }
    .messages-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .dark .messages-scroll::-webkit-scrollbar-thumb { background: #475569; }
    @media (max-width: 767px) {
        .chat-container { height: calc(100vh - 8rem); }
    }
</style>
@endpush


<div class="-mx-4 sm:-mx-6 lg:-mx-8 -mt-8"
     x-data="chat(@js($user->id), @js($initialConversationId))"
     x-init="init()">
    <div class="chat-container flex overflow-hidden bg-white dark:bg-slate-800">

        {{-- Conversation List Sidebar --}}
        <div class="w-full md:w-80 lg:w-96 flex-shrink-0 border-r border-gray-200 dark:border-slate-700 flex flex-col bg-gray-50 dark:bg-slate-900"
             :class="{ 'hidden md:flex': activeConversationId, 'flex': !activeConversationId }">

            {{-- Sidebar Header --}}
            <div class="p-4 border-b border-gray-200 dark:border-slate-700">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Messages</h2>
                    <button @click="openNewConversationModal()"
                            class="p-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white transition shadow-sm">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                    </button>
                </div>
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" x-model="searchQuery"
                           class="w-full pl-9 pr-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-400"
                           placeholder="Search conversations...">
                </div>
            </div>

            {{-- Conversation List --}}
            <div class="flex-1 overflow-y-auto" x-ref="conversationList">
                <template x-for="conv in filteredConversations" :key="conv.id">
                    <div @click="selectConversation(conv.id)"
                         :class="{ 'bg-sky-50 dark:bg-sky-900/20 border-l-2 border-sky-500': activeConversationId === conv.id, 'border-l-2 border-transparent hover:bg-gray-50 dark:hover:bg-slate-800/50': activeConversationId !== conv.id }"
                         class="flex items-center gap-3 px-4 py-3 cursor-pointer transition border-b border-gray-100 dark:border-slate-700/50">

                        {{-- Avatar --}}
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold"
                                 x-text="conv.initials"></div>
                            <span x-show="conv.is_online"
                                  class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="conv.displayName"></span>
                                <span class="text-xs text-gray-400 flex-shrink-0 ml-2" x-text="conv.lastMessageTime ? formatTime(conv.lastMessageTime) : ''"></span>
                            </div>
                            <div class="flex items-center justify-between mt-0.5">
                                <span class="text-xs text-gray-500 dark:text-slate-400 truncate" x-text="conv.lastMessagePreview || 'No messages yet'"></span>
                                <span x-show="conv.unread_count > 0"
                                      class="flex-shrink-0 ml-2 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-sky-500 rounded-full"
                                      x-text="conv.unread_count > 99 ? '99+' : conv.unread_count"></span>
                            </div>
                        </div>
                        <button @click.stop="requestDelete(conv.id, 'conversation')"
                                x-show="conv.created_by === currentUserId || {{ $user->isAdmin() ? 'true' : 'false' }}"
                                class="opacity-0 group-hover:opacity-100 flex-shrink-0 p-1.5 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 text-gray-400 hover:text-red-500 transition"
                                title="Delete conversation">
                            <i class="fa-regular fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </template>

                {{-- Empty state --}}
                <div x-show="filteredConversations.length === 0" class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                        <i class="fa-regular fa-message text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">No conversations yet</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-3">Start a new conversation to begin messaging</p>
                    <button @click="openNewConversationModal()"
                            class="px-4 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium transition shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> New Message
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Chat Area --}}
        <div class="flex-1 flex flex-col"
             :class="{ 'flex': activeConversationId, 'hidden md:flex': !activeConversationId }">

            {{-- Chat Header --}}
            <template x-if="activeConversationData">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex-shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Mobile back button --}}
                        <button @click="showSidebar = true; activeConversationId = null"
                                class="md:hidden p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-500">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                             x-text="activeConversationData.initials"></div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="activeConversationData.displayName"></h3>
                            <p class="text-xs text-gray-400 dark:text-slate-500 truncate" x-text="activeConversationData.participantInfo || ''"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button @click="togglePin(activeConversationId)"
                                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 hover:text-yellow-500 transition"
                                :class="{ 'text-yellow-500': activeConversationData.is_pinned }"
                                title="Pin conversation">
                            <i class="fa-solid fa-thumbtack text-sm"></i>
                        </button>
                        <button @click="toggleArchive(activeConversationId)"
                                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition"
                                title="Archive">
                            <i class="fa-regular fa-folder text-sm"></i>
                        </button>
                        <button @click="requestDelete(activeConversationId, 'conversation')"
                                x-show="activeConversationData.created_by === currentUserId || {{ $user->isAdmin() ? 'true' : 'false' }}"
                                class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 text-gray-400 hover:text-red-500 transition"
                                title="Delete conversation">
                            <i class="fa-regular fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </div>
            </template>

            {{-- Messages Area --}}
            <div class="flex-1 overflow-y-auto messages-scroll px-4 py-4 space-y-1 bg-gray-50 dark:bg-slate-800/50"
                 x-ref="messagesContainer"
                 @scroll="onScroll">

                {{-- Load more --}}
                <div x-show="hasMoreMessages" class="text-center py-3">
                    <button @click="loadMoreMessages()"
                            class="text-xs text-sky-500 hover:text-sky-600 font-medium transition"
                            x-text="isLoadingMore ? 'Loading...' : 'Load older messages'">
                    </button>
                </div>

                {{-- Messages --}}
                <template x-if="isLoadingMessages">
                    <div class="flex items-center justify-center py-12">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-gray-300 dark:text-slate-600"></i>
                    </div>
                </template>

                <div x-html="messagesHtml"></div>

                {{-- Scrolling anchor --}}
                <div x-ref="scrollAnchor"></div>

                {{-- Typing indicator --}}
                <div x-show="typingText" class="text-xs text-gray-400 dark:text-slate-500 italic px-1 py-1" x-text="typingText"></div>
            </div>

            {{-- Reply Preview --}}
            <div x-show="replyingTo" class="px-4 py-2 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700 flex items-center gap-3">
                <i class="fa-solid fa-reply text-gray-400 text-sm"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-600 dark:text-slate-300" x-text="'Replying to ' + (getReplyMessage(replyingTo)?.sender_name || '')"></p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 truncate" x-text="getReplyMessage(replyingTo)?.body || ''"></p>
                </div>
                <button @click="cancelReply()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Input Area --}}
            <div class="border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                {{-- File preview --}}
                <div x-show="selectedFile" class="flex items-center gap-3 mb-2 px-3 py-2 bg-gray-50 dark:bg-slate-700 rounded-lg">
                    <i class="fa-regular fa-file text-lg text-sky-500"></i>
                    <span class="text-sm text-gray-700 dark:text-slate-300 truncate flex-1" x-text="selectedFile?.name"></span>
                    <button @click="selectedFile = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="flex items-end gap-2">
                    <div class="flex-1 flex items-end gap-2 bg-gray-50 dark:bg-slate-700 rounded-xl px-3 py-2 border border-gray-200 dark:border-slate-600 focus-within:ring-2 focus-within:ring-sky-400 focus-within:border-sky-400">
                        <button @click="$refs.fileInput.click()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition flex-shrink-0 p-1" title="Attach file">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                        <textarea x-model="messageText"
                                  x-ref="messageInput"
                                  @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                                  @input="startTyping()"
                                  @keydown="startTyping()"
                                  placeholder="Type a message..."
                                  class="flex-1 bg-transparent border-0 outline-none resize-none text-sm text-gray-900 dark:text-white placeholder-gray-400 max-h-32"
                                  rows="1"
                                  style="scrollbar-width: thin;"></textarea>
                        <button @click="sendMessage()"
                                :disabled="!messageText.trim() && !selectedFile"
                                class="flex-shrink-0 p-1 text-sky-500 hover:text-sky-600 disabled:text-gray-300 dark:disabled:text-slate-600 transition"
                                :class="{ 'opacity-50 cursor-not-allowed': !messageText.trim() && !selectedFile }">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <input type="file" x-ref="fileInput" @change="selectedFile = $refs.fileInput.files[0] || null" class="hidden" accept="*">
            </div>

            {{-- Empty state (no active conversation) --}}
            <div x-show="!activeConversationId" class="flex-1 flex flex-col items-center justify-center bg-gray-50 dark:bg-slate-800/50">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                    <i class="fa-regular fa-comments text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Your Messages</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Select a conversation or start a new one</p>
                <button @click="openNewConversationModal()"
                        class="px-5 py-2.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium transition shadow-sm">
                    <i class="fa-solid fa-plus mr-1"></i> New Message
                </button>
            </div>
        </div>
    </div>

{{-- New Conversation Modal --}}
<div x-show="showNewConversationModal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div @click.outside="showNewConversationModal = false"
         class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">New Conversation</h3>
                <button @click="showNewConversationModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Type selector --}}
            <div class="flex gap-2 mb-4">
                <button @click="newConversationType = 'direct'"
                        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition"
                        :class="newConversationType === 'direct' ? 'bg-sky-500 text-white shadow-sm' : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600'">
                    <i class="fa-solid fa-user mr-1"></i> Direct
                </button>
                <button @click="newConversationType = 'group'"
                        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition"
                        :class="newConversationType === 'group' ? 'bg-sky-500 text-white shadow-sm' : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600'">
                    <i class="fa-solid fa-users mr-1"></i> Group
                </button>
            </div>

            {{-- Class selector for group (students only) --}}
            @if($user->isStudent() && $classes->isNotEmpty())
            <div x-show="newConversationType === 'group'" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Class (students only)</label>
                <select x-model="newConversationClassId"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm">
                    <option value="">Custom group</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Subject --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    Subject <span class="text-gray-400">(optional)</span>
                </label>
                <input type="text" x-model="newConversationSubject"
                       class="w-full rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm"
                       placeholder="What's this about?">
            </div>

            {{-- Users search/select --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                    <span x-text="newConversationType === 'direct' ? 'Search for a person' : 'Add participants'"></span>
                </label>
                <input type="text" x-model="userSearchQuery"
                       @input="searchUsers()"
                       class="w-full rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm mb-2"
                       placeholder="Type a name...">
                <div class="max-h-48 overflow-y-auto border border-gray-200 dark:border-slate-600 rounded-lg">
                    <template x-if="filteredUsers.length === 0">
                        <p class="text-sm text-gray-400 dark:text-slate-500 text-center py-4">No users found</p>
                    </template>
                    <template x-for="u in filteredUsers" :key="u.id">
                        <div @click="toggleUserSelection(u.id)"
                             class="flex items-center gap-3 px-3 py-2.5 cursor-pointer transition"
                             :class="newConversationUserIds.includes(u.id) ? 'bg-sky-50 dark:bg-sky-900/20' : 'hover:bg-gray-50 dark:hover:bg-slate-700/50'">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 x-text="getInitials(u.name)"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="u.name"></p>
                                <p class="text-xs text-gray-400" x-text="u.role"></p>
                            </div>
                            <div x-show="newConversationUserIds.includes(u.id)"
                                 class="w-5 h-5 rounded-full bg-sky-500 flex items-center justify-center">
                                <i class="fa-solid fa-check text-white text-xs"></i>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Initial message --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Initial message</label>
                <textarea x-model="newConversationInitialMessage"
                          class="w-full rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white text-sm"
                          rows="3" placeholder="Say hello..."></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <button @click="showNewConversationModal = false"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                    Cancel
                </button>
                <button @click="startNewConversation()"
                        :disabled="isCreatingConversation"
                        class="px-5 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-paper-plane mr-1"></i>
                    <span x-text="isCreatingConversation ? 'Creating...' : 'Start Conversation'"></span>
                </button>
            </div>
        </div>
    </div>
{{-- Confirm Modal --}}
<div x-show="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
            <i class="fa-regular fa-trash-can text-red-500 text-lg"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">Delete Conversation</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 text-center mb-6">Are you sure you want to delete this conversation and all its messages?</p>
        <div class="flex justify-end gap-3">
            <button @click="showConfirm = false"
                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                Cancel
            </button>
            <button @click="confirmDelete()"
                    class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition shadow-sm">
                Delete
            </button>
        </div>
    </div>
</div>

</div>

<script>
    function chat(currentUserId, initialConversationId) {
        return {
            // --- State ---
            currentUserId: currentUserId,
            activeConversationId: initialConversationId,
            conversations: @js($conversations->map(fn($c) => [
                'id' => $c->id,
                'subject' => $c->subject,
                'is_group' => $c->is_group,
                'group_type' => $c->group_type,
                'displayName' => $c->is_group
                    ? ($c->subject ?: 'Group')
                    : ($c->participants->first()?->name ?? 'Unknown'),
                'initials' => $c->is_group
                    ? 'G'
                    : str($c->participants->first()?->name ?? '?')->substr(0, 2)->upper(),
                'participantInfo' => $c->is_group
                    ? $c->participants->pluck('name')->implode(', ')
                    : ($c->participants->first()?->email ?? ''),
                'lastMessagePreview' => $c->last_message_preview,
                'lastMessageTime' => $c->last_message_time?->toISOString(),
                'unread_count' => $c->unread_count,
                'created_by' => $c->created_by,
                'is_pinned' => false,
                'is_online' => false,
                'participants' => $c->participants->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'email' => $p->email]),
            ])),
            messagesHtml: @js($messagesHtml ?? ''),
            hasMoreMessages: {{ ($totalMessages ?? 0) > 50 ? 'true' : 'false' }},
            nextPage: 2,
            isLoadingMessages: false,
            isLoadingMore: false,
            selectedFile: null,
            messageText: '',
            searchQuery: '',
            replyingTo: null,
            editingMessageId: null,
            editText: '',
            typingUsers: {},
            typingTimer: null,
            isTyping: false,
            showNewConversationModal: false,
            newConversationType: 'direct',
            newConversationSubject: '',
            newConversationClassId: '',
            newConversationInitialMessage: '',
            newConversationUserIds: [],
            userSearchQuery: '',
            availableUsers: @js($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->getRoleAttribute()])),
            isCreatingConversation: false,
            showConfirm: false,
            confirmId: null,
            confirmType: '',

            // --- Computed ---
            get filteredConversations() {
                if (!this.searchQuery) return this.conversations;
                const q = this.searchQuery.toLowerCase();
                return this.conversations.filter(c =>
                    c.displayName.toLowerCase().includes(q) ||
                    (c.lastMessagePreview && c.lastMessagePreview.toLowerCase().includes(q))
                );
            },

            get activeConversationData() {
                return this.conversations.find(c => c.id === this.activeConversationId) || null;
            },

            get filteredUsers() {
                if (!this.userSearchQuery) return this.availableUsers;
                const q = this.userSearchQuery.toLowerCase();
                return this.availableUsers.filter(u =>
                    u.name.toLowerCase().includes(q)
                );
            },

            get typingText() {
                const names = Object.values(this.typingUsers);
                if (names.length === 0) return '';
                if (names.length === 1) return `${names[0]} is typing...`;
                if (names.length === 2) return `${names[0]} and ${names[1]} are typing...`;
                return `${names[0]} and ${names.length - 1} others are typing...`;
            },

            // --- Methods ---
            init() {
                this.setupEcho();

                if (this.activeConversationId) {
                    this.setupConversationEcho(this.activeConversationId);
                }
            },

            setupEcho() {
                if (!window.Echo) return;

                window.Echo.private(`App.Models.User.${this.currentUserId}`)
                    .notification((notification) => {
                        if (notification.type === 'message' && notification.conversation_id) {
                            if (notification.conversation_id !== this.activeConversationId) {
                                this.refreshConversationList();
                            }
                        }
                    });
            },

            setupConversationEcho(conversationId) {
                if (!window.Echo) return;

                if (this._echoChannel) {
                    window.Echo.leave(`conversation.${this._echoConversationId}`);
                }

                this._echoConversationId = conversationId;
                this._echoChannel = window.Echo.channel(`conversation.${conversationId}`);

                this._echoChannel.listen('.MessageSent', (e) => {
                    if (e.sender_id !== this.currentUserId) {
                        this.appendMessageHtml(e.html);
                        this.updateConversationPreview(conversationId, e.body || '📎 File', e.created_at);
                    }
                });

                this._echoChannel.listen('.MessageEdited', (e) => {
                    const el = document.querySelector(`#message-${e.id} .text-sm.whitespace-pre-wrap`);
                    if (el) {
                        el.textContent = e.body;
                    }
                });

                this._echoChannel.listen('.MessageDeleted', (e) => {
                    const el = document.getElementById(`message-${e.id}`);
                    if (el) el.remove();
                });

                this._echoChannel.listen('.MessageReacted', (e) => {
                    this.refreshConversationList();
                });

                this._echoChannel.listenForWhisper('typing', (e) => {
                    if (e.user_id !== this.currentUserId) {
                        if (e.typing) {
                            this.typingUsers[e.user_id] = e.user_name;
                        } else {
                            delete this.typingUsers[e.user_id];
                        }
                    }
                });
            },

            selectConversation(id) {
                if (id === this.activeConversationId) return;

                this.activeConversationId = id;
                this.isLoadingMessages = true;
                this.replyingTo = null;
                this.editingMessageId = null;
                this.messageText = '';
                this.typingUsers = {};

                fetch(`/conversations/${id}/messages`)
                    .then(r => r.json())
                    .then(data => {
                        this.messagesHtml = data.html;
                        this.hasMoreMessages = data.hasMore;
                        this.nextPage = data.nextPage;
                        this.isLoadingMessages = false;

                        this.setupConversationEcho(id);
                        this.markAsRead(id);

                        this.$nextTick(() => this.$refs.scrollAnchor?.scrollIntoView());
                    })
                    .catch(() => {
                        this.isLoadingMessages = false;
                    });
            },

            appendMessageHtml(html) {
                const container = this.$refs.messagesContainer;
                if (container) {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    container.appendChild(temp.firstElementChild);
                    this.$nextTick(() => this.$refs.scrollAnchor?.scrollIntoView());
                }
            },

            loadMoreMessages() {
                if (this.isLoadingMore || !this.hasMoreMessages) return;
                this.isLoadingMore = true;

                fetch(`/conversations/${this.activeConversationId}/messages?page=${this.nextPage}`)
                    .then(r => r.json())
                    .then(data => {
                        const container = this.$refs.messagesContainer;
                        const firstMsg = container?.querySelector('[data-message-id]');
                        const scrollHeightBefore = container?.scrollHeight || 0;

                        const temp = document.createElement('div');
                        temp.innerHTML = data.html;
                        if (firstMsg && container) {
                            container.insertBefore(temp, firstMsg);
                        } else if (container) {
                            container.prepend(temp);
                        }

                        this.hasMoreMessages = data.hasMore;
                        this.nextPage = data.nextPage;

                        if (container) {
                            container.scrollTop = container.scrollHeight - scrollHeightBefore;
                        }
                    })
                    .finally(() => {
                        this.isLoadingMore = false;
                    });
            },

            sendMessage() {
                const text = this.messageText.trim();
                if (!text && !this.selectedFile) return;
                if (!this.activeConversationId) return;

                const formData = new FormData();
                if (text) formData.append('body', text);
                if (this.selectedFile) formData.append('file', this.selectedFile);
                if (this.replyingTo) formData.append('parent_id', this.replyingTo);

                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

                fetch(`/conversations/${this.activeConversationId}/messages`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': csrf },
                })
                .then(r => r.json())
                .then(data => {
                    this.appendMessageHtml(data.html);
                    this.messageText = '';
                    this.selectedFile = null;
                    this.replyingTo = null;

                    this.updateConversationPreview(this.activeConversationId, text || '📎 File', new Date().toISOString());

                    if (this._echoChannel) {
                        window.Echo.channel(`conversation.${this.activeConversationId}`)
                            .whisper('typing', { typing: false, user_id: this.currentUserId });
                    }
                })
                .catch(err => console.error('Failed to send message', err));
            },

            startTyping() {
                if (!this._echoChannel) return;
                if (!this.isTyping) {
                    this.isTyping = true;
                    this._echoChannel.whisper('typing', { typing: true, user_id: this.currentUserId, user_name: '{{ $user->name }}' });
                }
                clearTimeout(this.typingTimer);
                this.typingTimer = setTimeout(() => {
                    this.isTyping = false;
                    if (this._echoChannel) {
                        this._echoChannel.whisper('typing', { typing: false, user_id: this.currentUserId });
                    }
                }, 2000);
            },

            requestDelete(id, type) {
                this.confirmId = id;
                this.confirmType = type;
                this.showConfirm = true;
            },

            confirmDelete() {
                const id = this.confirmId;
                const type = this.confirmType;
                this.showConfirm = false;
                if (type === 'conversation') {
                    this._deleteConversation(id);
                }
            },

            deleteMessage(id) {
                if (!confirm('Delete this message?')) return;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/messages/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf },
                })
                .then(r => r.json())
                .then(() => {
                    const el = document.getElementById(`message-${id}`);
                    if (el) el.remove();
                })
                .catch(err => console.error('Failed to delete message', err));
            },

            editMessage(id) {
                const el = document.getElementById(`message-${id}`);
                if (!el) return;
                const textEl = el.querySelector('.text-sm.whitespace-pre-wrap');
                if (!textEl) return;
                this.editingMessageId = id;
                this.editText = textEl.textContent;
            },

            cancelEdit() {
                this.editingMessageId = null;
                this.editText = '';
            },

            saveEdit(id) {
                if (!this.editText.trim()) return;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/messages/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ body: this.editText }),
                })
                .then(r => r.json())
                .then(() => {
                    const el = document.getElementById(`message-${id}`);
                    if (el) {
                        const textEl = el.querySelector('.text-sm.whitespace-pre-wrap');
                        if (textEl) textEl.textContent = this.editText;
                        const editedSpan = el.querySelector('.italic');
                        if (!editedSpan) {
                            const timeEl = el.querySelector('.text-\\[10px\\], .text-gray-400');
                            if (timeEl && !el.querySelector('.italic')) {
                                const editTag = document.createElement('span');
                                editTag.className = 'italic';
                                editTag.textContent = ' (edited)';
                                timeEl.appendChild(editTag);
                            }
                        }
                    }
                    this.cancelEdit();
                })
                .catch(err => console.error('Failed to edit message', err));
            },

            replyToMessage(id) {
                this.replyingTo = id;
                this.$nextTick(() => this.$refs.messageInput?.focus());
            },

            cancelReply() {
                this.replyingTo = null;
            },

            getReplyMessage(id) {
                const el = document.getElementById(`message-${id}`);
                if (!el) return null;
                const senderEl = el.querySelector('.text-xs.font-medium');
                const textEl = el.querySelector('.text-sm.whitespace-pre-wrap');
                return {
                    id: id,
                    sender_name: senderEl?.textContent || 'Unknown',
                    body: textEl?.textContent || '',
                };
            },

            showForwardModal(id) {
                this.forwardMessageId = id;
                this.showForwardModal = true;
            },

            toggleReaction(messageId, reaction) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/messages/${messageId}/reactions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ reaction }),
                })
                .then(r => r.json())
                .then(() => {
                    // Refresh messages to update reaction display
                    if (this.activeConversationId) {
                        this.selectConversation(this.activeConversationId);
                    }
                })
                .catch(err => console.error('Failed to toggle reaction', err));
            },

            togglePin(id) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/conversations/${id}/pin`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                })
                .then(r => r.json())
                .then(data => {
                    const conv = this.conversations.find(c => c.id === id);
                    if (conv) conv.is_pinned = data.pinned;
                });
            },

            toggleArchive(id) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/conversations/${id}/archive`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                });
            },

            _deleteConversation(id) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/conversations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.conversations = this.conversations.filter(c => c.id !== id);
                        if (this.activeConversationId === id) {
                            this.activeConversationId = null;
                            this.messagesHtml = '';
                        }
                    }
                })
                .catch(err => console.error('Failed to delete conversation', err));
            },

            markAsRead(id) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                fetch(`/conversations/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                });
                const conv = this.conversations.find(c => c.id === id);
                if (conv) conv.unread_count = 0;
            },

            updateConversationPreview(conversationId, text, time) {
                const conv = this.conversations.find(c => c.id === conversationId);
                if (conv) {
                    conv.lastMessagePreview = text;
                    conv.lastMessageTime = time;
                }
            },

            refreshConversationList() {
                fetch('/conversations-list/json')
                    .then(r => r.json())
                    .then(data => {
                        if (Array.isArray(data)) {
                            this.conversations = data;
                        }
                    })
                    .catch(() => {});
            },

            formatTime(iso) {
                if (!iso) return '';
                const d = new Date(iso);
                const now = new Date();
                const diff = now - d;
                const days = Math.floor(diff / 86400000);

                if (days === 0) {
                    return d.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                }
                if (days === 1) return 'Yesterday';
                if (days < 7) return d.toLocaleDateString([], { weekday: 'short' });
                return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
            },

            getInitials(name) {
                if (!name) return '?';
                return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
            },

            toggleUserSelection(id) {
                const idx = this.newConversationUserIds.indexOf(id);
                if (idx === -1) {
                    this.newConversationUserIds.push(id);
                } else {
                    this.newConversationUserIds.splice(idx, 1);
                }
            },

            searchUsers() {
                // Filter is done via computed property
            },

            openNewConversationModal() {
                this.showNewConversationModal = true;
                this.newConversationType = 'direct';
                this.newConversationSubject = '';
                this.newConversationClassId = '';
                this.newConversationInitialMessage = '';
                this.newConversationUserIds = [];
                this.userSearchQuery = '';
            },

            startNewConversation() {
                if (this.isCreatingConversation) return;
                this.isCreatingConversation = true;

                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const data = {
                    type: this.newConversationType,
                    participant_ids: this.newConversationUserIds,
                    subject: this.newConversationSubject,
                    class_id: this.newConversationClassId || null,
                    initial_message: this.newConversationInitialMessage,
                };

                fetch('/conversations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(data),
                })
                .then(r => r.json())
                .then(data => {
                    this.showNewConversationModal = false;
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(err => {
                    console.error('Failed to create conversation', err);
                })
                .finally(() => {
                    this.isCreatingConversation = false;
                });
            },

            onScroll() {
                const container = this.$refs.messagesContainer;
                if (container && container.scrollTop < 100 && this.hasMoreMessages) {
                    this.loadMoreMessages();
                }
            },
        };
    }
</script>

</x-app-layout>
