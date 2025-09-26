<?php

namespace Database\Seeders;

use App\Http\Controllers\HouseOwnerController;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HouseOwner;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::create( [
            'name' => 'SuperAdmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);
        $houseOwners = [
            [
                'name' => 'House Owner',
                'email' => 'houseowner@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
            ],
            [
                'name' => 'House Owner 2',
                'email' => 'houseowner2@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
            ],
            [
                'name' => 'House Owner 3',
                'email' => 'houseowner3@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
            ],
           [
            'name' => 'House Owner 4',
            'email' => 'houseowner4@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
           ],
           [
            'name' => 'House Owner 5',
            'email' => 'houseowner5@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
           ]
        ];
        foreach ($houseOwners as $user) {
            $user = User::create($user);
            HouseOwner::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        $tenants = [
            [
                'name' => 'Tenant',
                'email' => 'tenant@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 2',
                'email' => 'tenant2@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 3',
                'email' => 'tenant3@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 4',
                'email' => 'tenant4@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 5',
                'email' => 'tenant5@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 6',
                'email' => 'tenant6@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 7',
                'email' => 'tenant7@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 8',
                'email' => 'tenant8@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 9',
                'email' => 'tenant9@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 10',
                'email' => 'tenant10@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 11',
                'email' => 'tenant11@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 12',
                'email' => 'tenant12@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'Tenant 13',
                'email' => 'tenant13@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ]
            ];
            $owner_id = 1;
            foreach ($tenants as $index=>$tenant) {
                $user = User::create($tenant);
                Tenant::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->id,
                    'house_owner_id' => $owner_id,
                ]);
                if(($index+1) % 4 == 0) {
                    $owner_id++;
                }
            }
          
          
        
    }
}
