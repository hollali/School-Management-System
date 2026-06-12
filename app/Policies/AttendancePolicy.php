<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Teacher');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Teacher')) return $attendance->teacher_id === $user->teacher?->id;
        return false;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Teacher')) return $attendance->teacher_id === $user->teacher?->id;
        return false;
    }
}
