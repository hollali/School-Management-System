<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;

class SendMessageNotification
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;
        $sender = $message->sender;

        if (!$conversation || !$sender) return;

        $participants = $conversation->participants()
            ->where('users.id', '!=', $sender->id)
            ->get();

        foreach ($participants as $participant) {
            $notification = AppNotification::create([
                'type' => 'message',
                'notifiable_type' => get_class($participant),
                'notifiable_id' => $participant->id,
                'data' => [
                    'title' => 'New Message: ' . ($conversation->subject ?? 'Conversation'),
                    'body' => $sender->name . ': ' . substr($message->body, 0, 200),
                    'action_url' => route('conversations.show', $conversation),
                    'type' => 'message',
                    'conversation_id' => $conversation->id,
                    'sender_name' => $sender->name,
                ],
                'read_at' => null,
            ]);

            broadcast(new NotificationBroadcast($notification));
        }
    }
}
