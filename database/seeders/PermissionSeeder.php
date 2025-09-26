<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $permissions = [
            // User permissions - SuperAdmin only
            [
                'name'=>"users.view",
                'description'=>"View users",
            ],
            [
                'name'=>"users.create",
                'description'=>"Create users",
            ],
            [
                'name'=>"users.update",
                'description'=>"Update users",
            ],
            [
                'name'=>"users.delete",
                'description'=>"Delete users",
            ],
            
            // Building permissions - SuperAdmin only
            [
                'name'=>"buildings.view",
                'description'=>"View buildings",
            ],
            [
                'name'=>"buildings.create",
                'description'=>"Create buildings",
            ],
            [
                'name'=>"buildings.update",
                'description'=>"Update buildings",
            ],
            [
                'name'=>"buildings.delete",
                'description'=>"Delete buildings",
            ],
            [
                'name'=>"buildings.attach_house_owner",
                'description'=>"Attach building to house owner",
            ],
            
            // Flat permissions - House Owner can manage their own flats
            [
                'name'=>"flats.view",
                'description'=>"View flats",
            ],
            [
                'name'=>"flats.create",
                'description'=>"Create flats",
            ],
            [
                'name'=>"flats.update",
                'description'=>"Update flats",
            ],
            [
                'name'=>"flats.delete",
                'description'=>"Delete flats",
            ],
            [
                'name'=>"flats.allocate_tenant",
                'description'=>"Allocate tenant to flat",
            ],
            [
                'name'=>"flats.remove_tenant",
                'description'=>"Remove tenant from flat",
            ],
            
            // Bill Category permissions - House Owner can manage their own
            [
                'name'=>"bill_categories.view",
                'description'=>"View bill categories",
            ],
            [
                'name'=>"bill_categories.create",
                'description'=>"Create bill categories",
            ],
            [
                'name'=>"bill_categories.update",
                'description'=>"Update bill categories",
            ],
            [
                'name'=>"bill_categories.delete",
                'description'=>"Delete bill categories",
            ],
       ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
