<?php

namespace App\Policies;

use App\Models\Fee;
use App\Models\User;

class FeePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Fee $fee): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return true;
        if ($user->hasRole('Student')) return $fee->student_id === $user->student?->id;
        if ($user->hasRole('Parent')) {
            return $user->parentProfile?->students->pluck('id')->contains($fee->student_id);
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Fee $fee): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Fee $fee): bool
    {
        return $user->hasRole('Admin');
    }

    public function generate(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
