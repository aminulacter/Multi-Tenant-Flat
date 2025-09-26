<?php

namespace App\Policies;

use App\Models\BillCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BillCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only House Owner can view bill categories
        return $user->role_id === 2;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BillCategory $billCategory): bool
    {
        // Only House Owner can view their own bill categories
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $billCategory->building_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only House Owner can create bill categories
        return $user->role_id === 2;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BillCategory $billCategory): bool
    {
        // Only House Owner can update their own bill categories
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $billCategory->building_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BillCategory $billCategory): bool
    {
        // Only House Owner can delete their own bill categories
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $billCategory->building_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BillCategory $billCategory): bool
    {
        // Only House Owner can restore their own bill categories
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $billCategory->building_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BillCategory $billCategory): bool
    {
        // Only House Owner can permanently delete their own bill categories
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $billCategory->building_owner_id === $houseOwner->id;
        }
        
        return false;
    }
}
