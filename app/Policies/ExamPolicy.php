<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Exam $exam): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Teacher');
    }

    public function update(User $user, Exam $exam): bool
    {
        if ($user->hasRole('Teacher')) return $exam->teacher_id === $user->teacher?->id;
        return false;
    }

    public function delete(User $user, Exam $exam): bool
    {
        if ($user->hasRole('Teacher')) return $exam->teacher_id === $user->teacher?->id;
        return false;
    }
}
