<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'house_owner_id',
        'name',
        'email',
        'address',
        'city',
        'zip',
    ];

    /**
     * Get the user that owns the tenant record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the house owner for the tenant.
     */
    public function houseOwner()
    {
        return $this->belongsTo(HouseOwner::class);
    }

    /**
     * Get the flats for the tenant.
     */
    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }
}
