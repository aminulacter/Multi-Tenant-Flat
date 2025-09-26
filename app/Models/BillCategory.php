<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'building_owner_id',
    ];

    public function houseOwner(): BelongsTo
    {
        return $this->belongsTo(HouseOwner::class, 'building_owner_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
