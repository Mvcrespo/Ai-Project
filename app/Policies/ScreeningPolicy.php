<?php

namespace App\Policies;

use App\Models\Screening;
use App\Models\User;

class ScreeningPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->type === 'A';
    }

    public function view(User $user, Screening $screening): bool
    {
        return $user->type === 'A';
    }

    public function create(User $user): bool
    {
        return $user->type === 'A';
    }

    public function update(User $user, Screening $screening): bool
    {
        return $user->type === 'A';
    }

    public function delete(User $user, Screening $screening): bool
    {
        return $user->type === 'A';
    }

    public function destroySingle(User $user, Screening $screening): bool
    {
        return $user->type === 'A';
    }

    public function restore(User $user, Screening $screening): bool
    {
        return $user->type === 'A';
    }

    public function forceDelete(User $user, Screening $screening): bool
    {
        return $user->type === 'A';
    }

    public function selectSession(User $user): bool
    {
        return $user->type === 'E';
    }

    public function validateTicket(User $user): bool
    {
        return $user->type === 'E';
    }
}
