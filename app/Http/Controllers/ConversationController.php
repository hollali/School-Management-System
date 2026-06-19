<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Helpers\ActivityLogger;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $conversations = Conversation::forUser($user->id)
            ->with(['participants' => function ($q) use ($user) {
                $q->where('users.id', '!=', $user->id);
            }, 'lastMessage' => function ($q) {
                $q->withTrashed()->with('sender');
            }])
            ->orderByPinned($user->id)
            ->get();

        $conversationIds = $conversations->pluck('id');

        $unreadCounts = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $user->id)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->selectRaw('conversation_id, COUNT(*) as count')
            ->groupBy('conversation_id')
            ->pluck('count', 'conversation_id');

        $conversations->each(function ($conv) use ($unreadCounts) {
            $conv->unread_count = $unreadCounts[$conv->id] ?? 0;
        });

        $activeConversation = null;
        $messages = collect();
        $initialConversationId = null;

        if ($request->has('conversation')) {
            $activeConversation = $conversations->firstWhere('id', (int) $request->conversation);
        }

        if (!$activeConversation && $conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
        }

        if ($activeConversation) {
            $initialConversationId = $activeConversation->id;

            $totalMessages = $activeConversation->messages()->count();
            $messages = $activeConversation->messages()
                ->with(['sender', 'reactions.user', 'parent.sender'])
                ->withCount('reactions')
                ->oldest()
                ->limit(50)
                ->get();

            $messageIds = $messages->pluck('id');
            $existingReads = MessageRead::whereIn('message_id', $messageIds)
                ->where('user_id', $user->id)
                ->pluck('message_id')
                ->toArray();

            $newReads = [];
            foreach ($messages as $msg) {
                if ($msg->sender_id !== $user->id && !in_array($msg->id, $existingReads)) {
                    $newReads[] = [
                        'message_id' => $msg->id,
                        'user_id' => $user->id,
                        'read_at' => now(),
                    ];
                }
            }
            if (!empty($newReads)) {
                MessageRead::insert($newReads);
            }
        }

        $availableUsers = $user->getMessagableUserIds();
        $users = User::whereIn('id', $availableUsers)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $classes = collect();
        if ($user->isStudent()) {
            $classes = $user->student->classes()->orderBy('name')->get();
        }

        $messagesHtml = '';
        if ($messages->isNotEmpty()) {
            $messagesHtml = view('conversations.partials.message-list', [
                'messages' => $messages,
                'user' => $user,
            ])->render();
        }

        return view('conversations.index', compact(
            'conversations',
            'activeConversation',
            'messages',
            'messagesHtml',
            'initialConversationId',
            'users',
            'classes',
            'user',
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'type' => 'required|in:direct,group',
            'participant_ids' => 'required_unless:type,group|array',
            'participant_ids.*' => 'exists:users,id',
            'subject' => 'nullable|string|max:255',
            'class_id' => 'nullable|exists:classes,id',
            'initial_message' => 'nullable|string|max:10000',
        ]);

        if ($validated['type'] === 'direct') {
            $otherUserId = (int) $validated['participant_ids'][0];
            $otherUser = User::findOrFail($otherUserId);
            if (!$user->canMessage($otherUser)) {
                return response()->json(['error' => 'You cannot message this user.'], 403);
            }

            $existingConvId = DB::table('conversation_user as cu1')
                ->join('conversation_user as cu2', 'cu1.conversation_id', '=', 'cu2.conversation_id')
                ->join('conversations', 'cu1.conversation_id', '=', 'conversations.id')
                ->where('conversations.is_group', false)
                ->where('cu1.user_id', $user->id)
                ->where('cu2.user_id', $otherUserId)
                ->value('cu1.conversation_id');

            if ($existingConvId) {
                return response()->json(['redirect' => route('conversations.index', ['conversation' => $existingConvId])]);
            }
        }

        $conversation = Conversation::create([
            'subject' => $validated['subject'] ?? ($validated['type'] === 'direct' ? '' : null),
            'created_by' => $user->id,
            'is_group' => $validated['type'] === 'group',
            'group_type' => $validated['type'] === 'group' ? ($validated['class_id'] ? 'class' : 'custom') : null,
            'class_id' => $validated['class_id'] ?? null,
        ]);

        $participantIds = [$user->id];

        if ($validated['type'] === 'direct') {
            $participantIds[] = (int) $validated['participant_ids'][0];
        } elseif ($validated['class_id']) {
            if (!$user->isStudent()) {
                return response()->json(['error' => 'Only students can create class-based groups.'], 403);
            }
            $class = SchoolClass::with('students.user')->findOrFail($validated['class_id']);
            $studentIds = $class->students->pluck('user_id')->toArray();
            $participantIds = array_merge($participantIds, $studentIds);
        } else {
            $requestedIds = $validated['participant_ids'] ?? [];
            $requestedUsers = User::whereIn('id', $requestedIds)->get();
            foreach ($requestedUsers as $requestedUser) {
                if (!$user->canHaveParticipant($requestedUser)) {
                    return response()->json([
                        'error' => 'You cannot add ' . $requestedUser->name . ' to this conversation.',
                    ], 403);
                }
            }
            $participantIds = array_merge($participantIds, $requestedIds);
        }

        $now = now();
        foreach (array_unique($participantIds) as $pid) {
            $conversation->participants()->attach($pid, [
                'role' => $pid === $user->id ? 'owner' : 'member',
                'joined_at' => $now,
            ]);
        }

        if ($validated['filled'] ?? $validated['initial_message'] ?? false) {
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'body' => $validated['initial_message'] ?? '',
            ]);
            broadcast(new MessageSent($message))->toOthers();
        }

        ActivityLogger::log(
            'created conversation',
            'conversation',
            $conversation->id,
            $validated['type'] === 'direct'
                ? 'Started a direct conversation'
                : 'Created group conversation: ' . ($validated['subject'] ?? 'Untitled')
        );

        return response()->json([
            'redirect' => route('conversations.index', ['conversation' => $conversation->id]),
        ]);
    }

    public function destroy(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $this->authorize('delete', $conversation);

        ActivityLogger::log(
            'deleted conversation',
            'conversation',
            $conversation->id,
            "Deleted conversation #{$conversation->id} ({$conversation->subject})"
        );

        $conversation->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('conversations.index')->with('success', 'Conversation deleted.');
    }

    public function fetchMessages(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $user = $request->user();
        $page = $request->get('page', 1);
        $perPage = 50;

        $messages = $conversation->messages()
            ->with(['sender', 'reactions.user', 'parent.sender', 'conversation'])
            ->withCount('reactions')
            ->oldest();

        if ($page === 1) {
            $messages = $messages->limit($perPage)->get();
            $hasMore = $conversation->messages()->count() > $perPage;
        } else {
            $offset = ($page - 1) * $perPage;
            $total = $conversation->messages()->count();
            $messages = $messages->skip($offset)->take($perPage)->get();
            $hasMore = ($offset + $perPage) < $total;
        }

        $messageIds = $messages->pluck('id');
        $existingReads = MessageRead::whereIn('message_id', $messageIds)
            ->where('user_id', $user->id)
            ->pluck('message_id')
            ->toArray();

        $newReads = [];
        foreach ($messages as $msg) {
            if ($msg->sender_id !== $user->id && !in_array($msg->id, $existingReads)) {
                $newReads[] = [
                    'message_id' => $msg->id,
                    'user_id' => $user->id,
                    'read_at' => now(),
                ];
            }
        }
        if (!empty($newReads)) {
            MessageRead::insert($newReads);
        }

        $html = view('conversations.partials.message-list', [
            'messages' => $messages,
            'user' => $user,
        ])->render();

        return response()->json([
            'messages' => $messages->map(function ($m) {
                return [
                    'id' => $m->id,
                    'sender_id' => $m->sender_id,
                    'sender_name' => $m->sender->name,
                    'sender_avatar' => $m->sender->profile_photo_url,
                    'body' => $m->body,
                    'type' => $m->type,
                    'file_name' => $m->file_name,
                    'file_url' => $m->file_url,
                    'file_icon' => $m->file_icon,
                    'file_size' => $m->file_size,
                    'parent_id' => $m->parent_id,
                    'forwarded_from' => $m->forwarded_from,
                    'edited_at' => $m->edited_at?->toISOString(),
                    'created_at' => $m->created_at->toISOString(),
                    'reactions' => $m->reactions->groupBy('reaction')->map(function ($r) {
                        return [
                            'count' => $r->count(),
                            'users' => $r->pluck('user.name'),
                            'has_reacted' => $r->contains('user_id', auth()->id()),
                        ];
                    }),
                ];
            }),
            'html' => $html,
            'hasMore' => $hasMore,
            'nextPage' => $page + 1,
            'total' => $conversation->messages()->count(),
        ]);
    }

    public function markAsRead(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $user = $request->user();

        $unreadMessages = $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->get();

        $newReads = [];
        foreach ($unreadMessages as $msg) {
            $newReads[] = [
                'message_id' => $msg->id,
                'user_id' => $user->id,
                'read_at' => now(),
            ];
        }
        if (!empty($newReads)) {
            MessageRead::insert($newReads);
        }

        $pivot = $conversation->participants()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        if ($pivot) {
            $pivot->last_read_at = now();
            $pivot->save();
        }

        return response()->json(['success' => true]);
    }

    public function toggleArchive(Request $request, Conversation $conversation)
    {
        $user = $request->user();
        $pivot = $conversation->participants()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        if ($pivot) {
            $pivot->is_archived = !$pivot->is_archived;
            $pivot->save();
        }

        return response()->json(['archived' => $pivot?->is_archived]);
    }

    public function togglePin(Request $request, Conversation $conversation)
    {
        $user = $request->user();
        $pivot = $conversation->participants()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        if ($pivot) {
            $pivot->is_pinned = !$pivot->is_pinned;
            $pivot->save();
        }

        return response()->json(['pinned' => $pivot?->is_pinned]);
    }

    public function searchUsers(Request $request)
    {
        $user = $request->user();
        $search = $request->get('q', '');

        $availableIds = $user->getMessagableUserIds();
        $query = User::whereIn('id', $availableIds);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email'])
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'avatar' => $u->profile_photo_url,
                    'role' => $u->getRoleAttribute(),
                ];
            });

        return response()->json($users);
    }

    public function getAvailableUsersJson(Request $request)
    {
        $user = $request->user();
        $availableIds = $user->getMessagableUserIds();

        $users = User::whereIn('id', $availableIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'avatar' => $u->profile_photo_url,
                    'role' => $u->getRoleAttribute(),
                ];
            });

        return response()->json($users);
    }

    public function conversationListJson(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::forUser($user->id)
            ->with(['participants' => function ($q) use ($user) {
                $q->where('users.id', '!=', $user->id);
            }, 'lastMessage' => function ($q) {
                $q->withTrashed()->with('sender');
            }])
            ->orderByPinned($user->id)
            ->get();

        $conversationIds = $conversations->pluck('id');

        $unreadCounts = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $user->id)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->selectRaw('conversation_id, COUNT(*) as count')
            ->groupBy('conversation_id')
            ->pluck('count', 'conversation_id');

        $result = $conversations->map(function ($conv) use ($unreadCounts) {
            return [
                'id' => $conv->id,
                'subject' => $conv->subject,
                'is_group' => $conv->is_group,
                'displayName' => $conv->is_group
                    ? ($conv->subject ?: 'Group')
                    : ($conv->participants->first()?->name ?? 'Unknown'),
                'initials' => $conv->is_group
                    ? 'G'
                    : str($conv->participants->first()?->name ?? '?')->substr(0, 2)->upper(),
                'participantInfo' => $conv->is_group
                    ? $conv->participants->pluck('name')->implode(', ')
                    : ($conv->participants->first()?->email ?? ''),
                'lastMessagePreview' => $conv->last_message_preview,
                'lastMessageTime' => $conv->last_message_time?->toISOString(),
                'unread_count' => $unreadCounts[$conv->id] ?? 0,
                'is_pinned' => false,
            ];
        });

        return response()->json($result);
    }
}
