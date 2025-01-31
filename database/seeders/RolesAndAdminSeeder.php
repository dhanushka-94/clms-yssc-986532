<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $staffRole = Role::create(['name' => 'staff']);
        $memberRole = Role::create(['name' => 'member']);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@yssc.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $admin->roles()->attach($adminRole);

        // Create manager user
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@yssc.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);
        $manager->roles()->attach($managerRole);
    }
}
