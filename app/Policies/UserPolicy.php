<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return ($user->type === 'A' || $user->type === 'E') && $user->id !== $model->id && ($model->type === 'A' || $model->type === 'E');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can view customers.
     */
    public function viewCustomers(User $user): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can block the model.
     */
    public function block(User $user, User $model): bool
    {
        return $user->type === 'A';
    }

    /**
     * Determine whether the user can unblock the model.
     */
    public function unblock(User $user, User $model): bool
    {
        return $user->type === 'A';
    }
}
