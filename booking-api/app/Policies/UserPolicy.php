<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function isAdmin(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }
}
