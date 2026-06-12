<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Result $result): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Teacher');
    }

    public function update(User $user, Result $result): bool
    {
        if ($user->hasRole('Teacher')) return $result->teacher_id === $user->teacher?->id;
        return false;
    }

    public function delete(User $user, Result $result): bool
    {
        if ($user->hasRole('Teacher')) return $result->teacher_id === $user->teacher?->id;
        return false;
    }
}
