<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return true;
        if ($user->hasRole('Student')) return $user->student?->id === $student->id;
        if ($user->hasRole('Parent')) return $student->parent_id === $user->parentProfile?->id;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole('Admin');
    }
}
