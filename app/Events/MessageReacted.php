<?php

namespace App\Events;

use App\Models\MessageReaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageReacted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public MessageReaction $reaction;

    public function __construct(MessageReaction $reaction)
    {
        $this->reaction = $reaction;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('conversation.' . $this->reaction->message->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->reaction->message_id,
            'user_id' => $this->reaction->user_id,
            'user_name' => $this->reaction->user->name,
            'reaction' => $this->reaction->reaction,
            'action' => 'added',
        ];
    }
}
