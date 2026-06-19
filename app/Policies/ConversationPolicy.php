<?php

namespace App\Policies;

use App\Helpers\ActivityLogger;
use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conversation $conversation): bool
    {
        if (!$conversation->isParticipant($user->id)) {
            ActivityLogger::log(
                'unauthorized conversation access',
                'conversation',
                $conversation->id,
                "User {$user->name} ({$user->getRoleAttribute()}) attempted to access conversation #{$conversation->id}"
            );
            return false;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function addParticipant(User $user, Conversation $conversation): bool
    {
        $pivot = $conversation->participants()->where('user_id', $user->id)->first();
        return $pivot && in_array($pivot->pivot->role, ['owner', 'admin']);
    }

    public function update(User $user, Conversation $conversation): bool
    {
        if ($conversation->created_by !== $user->id && !$user->isAdmin()) {
            ActivityLogger::log(
                'unauthorized conversation update',
                'conversation',
                $conversation->id,
                "User {$user->name} attempted to update conversation #{$conversation->id}"
            );
            return false;
        }
        return true;
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $conversation->created_by === $user->id ||
            $user->isAdmin();
    }
}
