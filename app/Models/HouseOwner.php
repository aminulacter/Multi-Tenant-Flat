<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseOwner extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'address',
        'city',
        'zip',
    ];

    /**
     * Get the user that owns the house owner record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tenants for the house owner.
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
    public function house()
    {
        return $this->hasMany(Building::class);
    }
    public function flats()
    {
        return $this->hasMany(Flat::class);
    }
    public function billCategories()
    {
        return $this->hasMany(BillCategory::class);
    }
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
