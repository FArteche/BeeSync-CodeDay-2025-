<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Apiario;

class ApiarioPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Apiario $apiario): bool
    {
        // O usuário pode ver o apiário se for o dono ou se estiver compartilhado com ele
        return $user->id === $apiario->user_id || $apiario->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Apiario $apiario): bool
    {
        return $user->id === $apiario->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Apiario $apiario): bool
    {
        return $user->id === $apiario->user_id;
    }
}
