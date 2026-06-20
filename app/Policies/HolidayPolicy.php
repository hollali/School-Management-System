<?php

namespace App\Policies;

use App\Models\Holiday;
use App\Models\User;

class HolidayPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Holiday $holiday): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Holiday $holiday): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Holiday $holiday): bool
    {
        return $user->hasRole('Admin');
    }
}
