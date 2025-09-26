<?php

namespace Database\Seeders;

use App\Models\BillCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billCategories = [
            [
                'name' => 'Electricity',
                'description' => 'Electricity bill',
                'building_owner_id' => 1,
            ],
            [
                'name' => 'Water',
                'description' => 'Water bill',
                'building_owner_id' => 1,
            ],
            [
                'name' => 'Gas',
                'description' => 'Gas bill',
                'building_owner_id' => 1,
            ],
            [
                'name' => 'Internet',
                'description' => 'Internet bill',
                'building_owner_id' => 1,
            ],
            [
                'name' => 'Other',
                'description' => 'Other bill',
                'building_owner_id' => 1,
            ],


            [
                'name' => 'Electricity',
                'description' => 'Electricity bill',
                'building_owner_id' => 2,
            ],
            [
                'name' => 'Water',
                'description' => 'Water bill',
                'building_owner_id' => 2,
            ],
            [
                'name' => 'Gas',
                'description' => 'Gas bill',
                'building_owner_id' => 2,
            ],
            [
                'name' => 'Internet',
                'description' => 'Internet bill',
                'building_owner_id' => 2,
            ],
            [
                'name' => 'Other',
                'description' => 'Other bill',
                'building_owner_id' => 2,
            ]
        ];
        foreach ($billCategories as $billCategory) {
            BillCategory::create($billCategory);
        }
    }
}
