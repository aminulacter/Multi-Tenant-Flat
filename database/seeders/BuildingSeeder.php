<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            [
                'name' => 'Building 1',
                'house_owner_id' => 1,
                'address' => 'Address 1',
            ],
            [
                'name' => 'Building 2',
                'house_owner_id' => 1,
                'address' => 'Address 2',
            ],
            [
                'name' => 'Building 3',
                'house_owner_id' => 2,
                'address' => 'Address 3',
            ],
            [
                'name' => 'Building 4',
                'house_owner_id' => 2,
                'address' => 'Address 4',
            ],
            [
                'name' => 'Building 5',
                'house_owner_id' => 3,
                'address' => 'Address 5',
            ],
            [
                'name' => 'Building 6',
                'house_owner_id' => 3,
                'address' => 'Address 6',
            ],
            [
                'name' => 'Building 7',
                'house_owner_id' => 4,
                'address' => 'Address 7',
            ],
            [
                'name' => 'Building 8',
                'house_owner_id' => 4,
                'address' => 'Address 8',
            ]
        ];

        foreach ($buildings as $building) {
            Building::create($building);
        }
    }
}
