<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $assignment->teacher_id === $user->teacher?->id;
        if ($user->hasRole('Student')) return true;
        if ($user->hasRole('Parent')) return true;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Teacher');
    }

    public function update(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole('Teacher')) return $assignment->teacher_id === $user->teacher?->id;
        return false;
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole('Teacher')) return $assignment->teacher_id === $user->teacher?->id;
        return false;
    }
}
