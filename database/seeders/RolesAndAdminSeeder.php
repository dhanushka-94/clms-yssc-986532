<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access'
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Club management access'
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Staff access'
            ],
            [
                'name' => 'player',
                'display_name' => 'Player',
                'description' => 'Player access'
            ],
            [
                'name' => 'member',
                'display_name' => 'Member',
                'description' => 'Member access'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@yssc.com',
            'password' => Hash::make('password'),
        ]);

        // Assign admin role
        $admin->roles()->attach(Role::where('name', 'admin')->first());

        // Create manager user
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@yssc.com',
            'password' => Hash::make('password'),
        ]);

        // Assign manager role
        $manager->roles()->attach(Role::where('name', 'manager')->first());
    }
}
