<?php

namespace App\Policies;

use App\Models\AssignmentFeedback;
use App\Models\User;

class AssignmentFeedbackPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher') || $user->hasRole('Student');
    }

    public function view(User $user, AssignmentFeedback $feedback): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Teacher')) {
            return $feedback->submission->assignment->teacher_id === $user->teacher?->id;
        }

        if ($user->hasRole('Student')) {
            return $feedback->submission->student_id === $user->student?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Teacher');
    }

    public function update(User $user, AssignmentFeedback $feedback): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Teacher')) {
            return $feedback->submission->assignment->teacher_id === $user->teacher?->id;
        }

        return false;
    }

    public function delete(User $user, AssignmentFeedback $feedback): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Teacher')) {
            return $feedback->submission->assignment->teacher_id === $user->teacher?->id;
        }

        return false;
    }
}
