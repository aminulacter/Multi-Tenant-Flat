<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flat extends Model
{
    protected $fillable = [
        'name',
        'building_id',
        'tenant_id',
        'house_owner_id',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function houseOwner(): BelongsTo
    {
        return $this->belongsTo(HouseOwner::class);
    }
}
