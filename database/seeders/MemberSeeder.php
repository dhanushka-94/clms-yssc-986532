<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                'membership_number' => 'MEM2023001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'nic' => '199012345678',
                'phone' => '0771234567',
                'whatsapp_number' => '0771234567',
                'address' => '123 Main St, Colombo',
                'date_of_birth' => '1990-01-15',
                'joined_date' => '2023-01-01',
                'membership_fee' => 5000.00,
                'status' => 'active',
            ],
            [
                'membership_number' => 'MEM2023002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'nic' => '199112345678',
                'phone' => '0772345678',
                'whatsapp_number' => '0772345678',
                'address' => '456 Park Ave, Kandy',
                'date_of_birth' => '1991-03-20',
                'joined_date' => '2023-02-01',
                'membership_fee' => 5000.00,
                'status' => 'active',
            ],
        ];

        foreach ($members as $memberData) {
            // Create user account for member
            $user = User::create([
                'name' => $memberData['first_name'] . ' ' . $memberData['last_name'],
                'email' => strtolower($memberData['first_name']) . '.' . strtolower($memberData['last_name']) . '@example.com',
                'password' => Hash::make('password'),
            ]);

            // Create member with user_id
            $memberData['user_id'] = $user->id;
            Member::create($memberData);
        }
    }
}
