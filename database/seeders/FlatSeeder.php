<?php

namespace Database\Seeders;

use App\Models\Flat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flats = [
            [
                'name' => 'Flat 1',
                'building_id' => 1,
                'tenant_id' => 1,
                'house_owner_id' => 1,
            ],
            [
                'name' => 'Flat 2',
                'building_id' => 1,
                'tenant_id' => 2,
                'house_owner_id' => 1,
            ],
            [
                'name' => 'Flat 3',
                'building_id' => 2,
                'tenant_id' => 3,
                'house_owner_id' => 1,
            ],
            [
                'name' => 'Flat 4',
                'building_id' => 2,
                'tenant_id' => 4,
                'house_owner_id' => 1,
            ],
            [
                'name' => 'Flat 5',
                'building_id' => 3,
                'tenant_id' => 5,
                'house_owner_id' => 2,
            ],
            [
                'name' => 'Flat 6',
                'building_id' => 3,
                'tenant_id' => 6,
                'house_owner_id' => 2,
            ],
            
            [
                'name' => 'Flat 7',
                'building_id' => 4,
                'tenant_id' => 7,
                'house_owner_id' => 2,
            ],
            
            [
                'name' => 'Flat 8',
                'building_id' => 4,
                'tenant_id' => 8,
                'house_owner_id' => 2,
            ],
            
        ];
        foreach ($flats as $flat) {
            Flat::create($flat);
        }
    }
}
