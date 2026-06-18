<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($announcement->target_role && strtolower($announcement->target_role) !== strtolower($user->role)) {
            return false;
        }

        if ($announcement->target_role === null) return true;

        return strtolower($user->role) === strtolower($announcement->target_role);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Teacher')) {
            return $announcement->published_by === $user->id;
        }

        return false;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Teacher')) {
            return $announcement->published_by === $user->id;
        }

        return false;
    }
}
