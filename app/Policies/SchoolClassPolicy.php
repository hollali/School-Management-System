<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;

class SchoolClassPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SchoolClass $schoolClass): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $schoolClass->teacher_id === $user->teacher?->id;
        if ($user->hasRole('Student')) {
            return $user->student?->classes->contains('id', $schoolClass->id);
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, SchoolClass $schoolClass): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, SchoolClass $schoolClass): bool
    {
        return $user->hasRole('Admin');
    }
}
