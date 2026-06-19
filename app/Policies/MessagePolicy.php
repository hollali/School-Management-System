<?php

namespace App\Policies;

use App\Helpers\ActivityLogger;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user, Conversation $conversation): bool
    {
        $allowed = $conversation->isParticipant($user->id);
        if (!$allowed) {
            ActivityLogger::log(
                'unauthorized messages access',
                'conversation',
                $conversation->id,
                "User {$user->name} ({$user->getRoleAttribute()}) attempted to view messages in conversation #{$conversation->id}"
            );
        }
        return $allowed;
    }

    public function view(User $user, Message $message): bool
    {
        return $message->conversation->isParticipant($user->id);
    }

    public function create(User $user, Conversation $conversation): bool
    {
        return $conversation->isParticipant($user->id);
    }

    public function update(User $user, Message $message): bool
    {
        if ($message->trashed()) return false;
        return $message->isOwnedBy($user->id) && $message->isEditable();
    }

    public function delete(User $user, Message $message): bool
    {
        if ($message->trashed()) return false;
        return $message->isOwnedBy($user->id) ||
            $message->conversation->created_by === $user->id ||
            $user->isAdmin();
    }
}
