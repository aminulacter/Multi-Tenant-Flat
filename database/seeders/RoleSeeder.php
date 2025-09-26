<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $roles = [
        [
            'name' => 'SuperAdmin',
            'description' => 'SuperAdmin role',
        ],
        [
            'name' => 'House Owner',
            'description' => 'House Owner role',
        ],
        [
            'name' => 'Tenant',
            'description' => 'Tenant role',
        ],
       ];

       foreach ($roles as $role) {
        Role::create($role);
       }
    }
}
