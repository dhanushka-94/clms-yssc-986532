<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create staff user
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        // Create member user
        User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'role' => 'member',
        ]);

        // Create player user
        User::create([
            'name' => 'Player User',
            'email' => 'player@example.com',
            'password' => Hash::make('password123'),
            'role' => 'player',
        ]);
    }
} 