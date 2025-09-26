<?php

namespace App\Policies;

use App\Models\Flat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FlatPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // SuperAdmin can view all flats, House Owner can view their own flats
        return $user->role_id === 1 || $user->role_id === 2;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Flat $flat): bool
    {
        // SuperAdmin can view all flats
        if ($user->role_id === 1) {
            return true;
        }
        
        // House Owner can view their own flats
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $flat->house_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // SuperAdmin and House Owner can create flats
        return $user->role_id === 1 || $user->role_id === 2;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Flat $flat): bool
    {
        // SuperAdmin can update all flats
        if ($user->role_id === 1) {
            return true;
        }
        
        // House Owner can update their own flats
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $flat->house_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Flat $flat): bool
    {
        // SuperAdmin can delete all flats
        if ($user->role_id === 1) {
            return true;
        }
        
        // House Owner can delete their own flats
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $flat->house_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Flat $flat): bool
    {
        // SuperAdmin can restore all flats
        if ($user->role_id === 1) {
            return true;
        }
        
        // House Owner can restore their own flats
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $flat->house_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Flat $flat): bool
    {
        // SuperAdmin can permanently delete all flats
        if ($user->role_id === 1) {
            return true;
        }
        
        // House Owner can permanently delete their own flats
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $flat->house_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can allocate tenant to flat.
     */
    public function allocateTenant(User $user, Flat $flat): bool
    {
        // SuperAdmin can allocate tenants to all flats
        if ($user->role_id === 1) {
            return true;
        }
        
        // House Owner can allocate tenants to their own flats
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $flat->house_owner_id === $houseOwner->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can remove tenant from flat.
     */
    public function removeTenant(User $user, Flat $flat): bool
    {
        // SuperAdmin can remove tenants from all flats
        if ($user->role_id === 1) {
            return true;
        }
        
        // House Owner can remove tenants from their own flats
        if ($user->role_id === 2) {
            $houseOwner = $user->houseOwner;
            return $houseOwner && $flat->house_owner_id === $houseOwner->id;
        }
        
        return false;
    }
}
