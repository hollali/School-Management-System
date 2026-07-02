<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;

class QuestionBankPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher');
    }

    public function view(User $user, QuestionBank $questionBank): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $questionBank->teacher_id === $user->teacher?->id;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher');
    }

    public function update(User $user, QuestionBank $questionBank): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $questionBank->teacher_id === $user->teacher?->id;
        return false;
    }

    public function delete(User $user, QuestionBank $questionBank): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return $questionBank->teacher_id === $user->teacher?->id;
        return false;
    }
}
