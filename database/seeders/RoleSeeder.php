<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrator with full access'
            ],
            [
                'name' => 'manager',
                'description' => 'Club manager with access to most features'
            ],
            [
                'name' => 'staff',
                'description' => 'Staff member with limited access'
            ],
            [
                'name' => 'member',
                'description' => 'Regular club member'
            ],
            [
                'name' => 'player',
                'description' => 'Club player'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
