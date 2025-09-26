<?php

namespace App\Providers;

use App\Models\BillCategory;
use App\Models\Building;
use App\Models\Flat;
use App\Models\User;
use App\Policies\BillCategoryPolicy;
use App\Policies\BuildingPolicy;
use App\Policies\FlatPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Building::class => BuildingPolicy::class,
        Flat::class => FlatPolicy::class,
        BillCategory::class => BillCategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
