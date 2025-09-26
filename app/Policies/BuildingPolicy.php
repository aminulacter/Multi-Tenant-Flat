<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BuildingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only SuperAdmin can view buildings
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Building $building): bool
    {
        // Only SuperAdmin can view buildings
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only SuperAdmin can create buildings
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Building $building): bool
    {
        // Only SuperAdmin can update buildings
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Building $building): bool
    {
        // Only SuperAdmin can delete buildings
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Building $building): bool
    {
        // Only SuperAdmin can restore buildings
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Building $building): bool
    {
        // Only SuperAdmin can permanently delete buildings
        return $user->role_id === 1;
    }

    /**
     * Determine whether the user can attach house owner to building.
     */
    public function attachHouseOwner(User $user, Building $building): bool
    {
        // Only SuperAdmin can attach house owner to building
        return $user->role_id === 1;
    }
}
