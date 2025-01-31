<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClubSettings;

class ClubSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClubSettings::create([
            'name' => 'Young Sports Stars Club',
            'address' => '123 Sports Avenue, Colombo 05, Sri Lanka',
            'phone' => '+94 11 234 5678',
            'email' => 'info@yssc.lk',
            'description' => 'Nurturing Young Sports Talent',
            'registration_number' => 'REG/2024/001',
            'tax_number' => 'TAX/2024/001',
        ]);
    }
}
