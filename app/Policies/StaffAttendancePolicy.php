<?php

namespace App\Policies;

use App\Models\StaffAttendance;
use App\Models\User;

class StaffAttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher');
    }

    public function view(User $user, StaffAttendance $staffAttendance): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $staffAttendance->teacher_id === $user->teacher?->id;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, StaffAttendance $staffAttendance): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, StaffAttendance $staffAttendance): bool
    {
        return $user->hasRole('Admin');
    }

    public function checkIn(User $user): bool
    {
        return $user->hasRole('Teacher');
    }
}
