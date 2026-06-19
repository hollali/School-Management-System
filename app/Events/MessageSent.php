<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public Message $message;
    public User $sender;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->sender = $message->sender;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        $view = view('conversations.partials.message', ['message' => $this->message])->render();

        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'sender_avatar' => $this->sender->profile_photo_url,
            'body' => $this->message->body,
            'type' => $this->message->type,
            'file_name' => $this->message->file_name,
            'file_url' => $this->message->file_url,
            'file_icon' => $this->message->file_icon,
            'file_size' => $this->message->file_size,
            'parent_id' => $this->message->parent_id,
            'forwarded_from' => $this->message->forwarded_from,
            'created_at' => $this->message->created_at->toISOString(),
            'html' => $view,
        ];
    }
}
