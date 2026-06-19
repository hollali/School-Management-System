<?php

namespace App\Http\Controllers;

use App\Events\MessageDeleted;
use App\Events\MessageEdited;
use App\Events\MessageSent;
use App\Helpers\ActivityLogger;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $user = $request->user();
        $perPage = 50;
        $page = $request->get('page', 1);

        $messagesQuery = $conversation->messages()
            ->with(['sender', 'reactions.user', 'parent.sender', 'conversation'])
            ->withCount('reactions')
            ->oldest();

        $total = $messagesQuery->count();

        if ($page === 1) {
            $messages = $messagesQuery->limit($perPage)->get();
        } else {
            $offset = ($page - 1) * $perPage;
            $messages = $messagesQuery->skip($offset)->take($perPage)->get();
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
                    'is_owned' => $m->sender_id === auth()->id(),
                    'deleted_at' => $m->deleted_at?->toISOString(),
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
            'hasMore' => ($offset ?? 0) + $perPage < $total,
            'nextPage' => $page + 1,
            'total' => $total,
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('create', [Message::class, $conversation]);

        $request->validate([
            'body' => 'required_without:file|string|max:10000',
            'file' => 'nullable|file|max:51200|mimetypes:' . implode(',', config('messaging.allowed_mime_types', [])),
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        $user = $request->user();

        $data = [
            'sender_id' => $user->id,
            'body' => $request->body ?? '',
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('message-attachments', 'public');

            $data['type'] = 'file';
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_mime'] = $file->getMimeType();
        }

        if ($request->parent_id) {
            $data['parent_id'] = $request->parent_id;
        }

        $message = $conversation->messages()->create($data);
        $message->load('sender');
        $message->setRelation('conversation', $conversation);

        broadcast(new MessageSent($message))->toOthers();

        ActivityLogger::log(
            'sent message',
            'message',
            $message->id,
            "Sent a message in conversation #{$conversation->id}"
        );

        $html = view('conversations.partials.message', ['message' => $message])->render();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'sender_avatar' => $message->sender->profile_photo_url,
                'body' => $message->body,
                'type' => $message->type,
                'file_name' => $message->file_name,
                'file_url' => $message->file_url,
                'file_icon' => $message->file_icon,
                'file_size' => $message->file_size,
                'parent_id' => $message->parent_id,
                'forwarded_from' => $message->forwarded_from,
                'edited_at' => null,
                'created_at' => $message->created_at->toISOString(),
                'is_owned' => true,
            ],
            'html' => $html,
        ]);
    }

    public function update(Request $request, Message $message)
    {
        $this->authorize('update', $message);

        $request->validate([
            'body' => 'required|string|max:10000',
        ]);

        $message->update([
            'body' => $request->body,
            'edited_at' => now(),
        ]);

        broadcast(new MessageEdited($message))->toOthers();

        ActivityLogger::log(
            'edited message',
            'message',
            $message->id,
            "Edited message in conversation #{$message->conversation_id}"
        );

        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'edited_at' => $message->edited_at->toISOString(),
        ]);
    }

    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);

        $conversationId = $message->conversation_id;

        $message->delete();

        broadcast(new MessageDeleted($message))->toOthers();

        ActivityLogger::log(
            'deleted message',
            'message',
            $message->id,
            "Deleted message in conversation #{$conversationId}"
        );

        return response()->json(['success' => true]);
    }

    public function forward(Request $request, Message $message)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        $targetConversation = Conversation::findOrFail($request->conversation_id);
        $user = $request->user();

        $this->authorize('create', [Message::class, $targetConversation]);

        $forwarded = $targetConversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $message->body,
            'type' => $message->type,
            'file_path' => $message->file_path,
            'file_name' => $message->file_name,
            'file_size' => $message->file_size,
            'file_mime' => $message->file_mime,
            'forwarded_from' => $message->id,
        ]);

        $forwarded->load('sender');

        broadcast(new MessageSent($forwarded))->toOthers();

        ActivityLogger::log(
            'forwarded message',
            'message',
            $forwarded->id,
            "Forwarded message from #{$message->id} to conversation #{$targetConversation->id}"
        );

        $html = view('conversations.partials.message', ['message' => $forwarded])->render();

        return response()->json([
            'message' => [
                'id' => $forwarded->id,
                'sender_id' => $forwarded->sender_id,
                'sender_name' => $forwarded->sender->name,
                'sender_avatar' => $forwarded->sender->profile_photo_url,
                'body' => $forwarded->body,
                'type' => $forwarded->type,
                'file_name' => $forwarded->file_name,
                'file_url' => $forwarded->file_url,
                'file_icon' => $forwarded->file_icon,
                'file_size' => $forwarded->file_size,
                'forwarded_from' => $forwarded->forwarded_from,
                'created_at' => $forwarded->created_at->toISOString(),
            ],
            'html' => $html,
        ]);
    }
}
