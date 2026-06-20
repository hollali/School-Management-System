<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Teacher')) return true;
        if ($user->hasRole('Student')) return $payment->student_id === $user->student?->id;
        if ($user->hasRole('Parent')) {
            return $user->parentProfile?->students->pluck('id')->contains($payment->student_id);
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Parent');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('Admin');
    }
}
