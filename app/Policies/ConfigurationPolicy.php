<?php

namespace App\Policies;

use App\Models\Configuration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConfigurationPolicy
{
    use HandlesAuthorization;

    public function view(User $user): bool
    {
        return $user->type === 'A';
    }

    public function salesByYear(User $user): bool
    {
        return $user->type === 'A';
    }

    public function overallStats(User $user): bool
    {
        return $user->type === 'A';
    }

    public function topMoviesLastYear(User $user): bool
    {
        return $user->type === 'A';
    }

    public function topMoviesThisYear(User $user): bool
    {
        return $user->type === 'A';
    }

    public function getConfig(User $user): bool
    {
        return $user->type === 'A';
    }

    public function updateConfig(User $user): bool
    {
        return $user->type === 'A';
    }

    public function topGenres(User $user): bool
    {
        return $user->type === 'A';
    }

    public function topTheaters(User $user): bool
    {
        return $user->type === 'A';
    }

}


