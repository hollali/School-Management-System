<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Teacher') || $user->hasRole('Student');
    }

    public function view(User $user, Submission $submission): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Teacher')) {
            return $submission->assignment->teacher_id === $user->teacher?->id;
        }

        if ($user->hasRole('Student')) {
            return $submission->student_id === $user->student?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Student');
    }

    public function update(User $user, Submission $submission): bool
    {
        if ($user->hasRole('Student')) {
            return $submission->student_id === $user->student?->id && $submission->status !== 'graded';
        }
        return false;
    }

    public function delete(User $user, Submission $submission): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Teacher')) {
            return $submission->assignment->teacher_id === $user->teacher?->id;
        }

        return false;
    }

    public function retract(User $user, Submission $submission): bool
    {
        if ($user->hasRole('Student')) {
            return $submission->student_id === $user->student?->id && $submission->status !== 'graded';
        }
        return false;
    }

    public function reject(User $user, Submission $submission): bool
    {
        if ($user->hasRole('Teacher')) {
            return $submission->assignment->teacher_id === $user->teacher?->id && $submission->status !== 'graded';
        }
        return false;
    }
}
