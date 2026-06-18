<?php

namespace App\Events;

use App\Models\AppNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class NotificationBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public int $notificationId;
    public string $type;
    public array $data;
    public string $createdAt;
    public int $notifiableId;
    public bool $isUnread;

    public function __construct(AppNotification $notification)
    {
        $this->notificationId = $notification->id;
        $this->type = $notification->type;
        $this->data = $notification->data ?? [];
        $this->createdAt = $notification->created_at->toISOString();
        $this->notifiableId = $notification->notifiable_id;
        $this->isUnread = is_null($notification->read_at);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('notifications.' . $this->notifiableId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.received';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notificationId,
            'type' => $this->type,
            'data' => $this->data,
            'created_at' => $this->createdAt,
            'is_unread' => $this->isUnread,
        ];
    }
}
