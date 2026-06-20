<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $attendance->teacher_id === $user->teacher?->id;
        if ($user->hasRole('Student')) {
            return $attendance->schoolClass?->students()
                ->where('student_id', $user->student?->id)
                ->exists() ?? false;
        }
        if ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            return $attendance->records()->whereIn('student_id', $studentIds)->exists();
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Teacher') || $user->hasRole('Admin');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $attendance->teacher_id === $user->teacher?->id;
        return false;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $attendance->teacher_id === $user->teacher?->id;
        return false;
    }

    public function mark(User $user): bool
    {
        return $user->hasRole('Teacher') || $user->hasRole('Admin');
    }

    public function correct(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
