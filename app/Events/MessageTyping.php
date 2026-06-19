<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public User $user;
    public int $conversationId;
    public bool $typing;

    public function __construct(User $user, int $conversationId, bool $typing = true)
    {
        $this->user = $user;
        $this->conversationId = $conversationId;
        $this->typing = $typing;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('conversation.' . $this->conversationId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'conversation_id' => $this->conversationId,
            'typing' => $this->typing,
        ];
    }
}
