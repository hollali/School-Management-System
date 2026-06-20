<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\User;

class ReceiptPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Receipt $receipt): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Student')) {
            return $receipt->payment->student_id === $user->student?->id;
        }
        if ($user->hasRole('Parent')) {
            return $user->parentProfile?->students->pluck('id')->contains($receipt->payment->student_id);
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Receipt $receipt): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Receipt $receipt): bool
    {
        return $user->hasRole('Admin');
    }
}
