<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = [
        'name',
        'house_owner_id',
        'address',
    ];

    /**
     * Get the house owner that owns the building.
     */
    public function houseOwner(): BelongsTo
    {
        return $this->belongsTo(HouseOwner::class);
    }

    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }

    /**
     * Get the units for the building.
     */
    // public function units(): HasMany
    // {
    //     return $this->hasMany(\App\Models\Unit::class);
    // }
}
