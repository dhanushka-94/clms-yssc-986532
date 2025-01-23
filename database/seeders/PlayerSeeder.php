<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = [
            [
                'player_id' => 'PLY2023001',
                'first_name' => 'Michael',
                'last_name' => 'Fernando',
                'nic' => '199512345678',
                'ffsl_number' => 'FFSL2023001',
                'phone' => '0775678901',
                'whatsapp_number' => '0775678901',
                'address' => '567 Beach Rd, Negombo',
                'position' => 'striker',
                'jersey_number' => '10',
                'date_of_birth' => '1995-06-20',
                'joined_date' => '2023-01-01',
                'contract_amount' => 100000.00,
                'contract_start_date' => '2023-01-01',
                'contract_end_date' => '2024-12-31',
                'status' => 'active',
                'achievements' => 'Top scorer 2022',
            ],
            [
                'player_id' => 'PLY2023002',
                'first_name' => 'Ashan',
                'last_name' => 'Perera',
                'nic' => '199612345678',
                'ffsl_number' => 'FFSL2023002',
                'phone' => '0776789012',
                'whatsapp_number' => '0776789012',
                'address' => '890 Mountain Rd, Kandy',
                'position' => 'midfielder',
                'jersey_number' => '8',
                'date_of_birth' => '1996-09-15',
                'joined_date' => '2023-01-01',
                'contract_amount' => 90000.00,
                'contract_start_date' => '2023-01-01',
                'contract_end_date' => '2024-12-31',
                'status' => 'active',
                'achievements' => 'Best midfielder 2022',
            ],
        ];

        foreach ($players as $playerData) {
            // Create user account for player
            $user = User::create([
                'name' => $playerData['first_name'] . ' ' . $playerData['last_name'],
                'email' => strtolower($playerData['first_name']) . '.' . strtolower($playerData['last_name']) . '@example.com',
                'password' => Hash::make('password'),
            ]);

            // Create player with user_id
            $playerData['user_id'] = $user->id;
            Player::create($playerData);
        }
    }
}
