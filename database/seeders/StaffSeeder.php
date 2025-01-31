<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffMembers = [
            [
                'employee_id' => 'EMP2023001',
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'nic' => '198512345678',
                'phone' => '0773456789',
                'whatsapp_number' => '0773456789',
                'address' => '789 Lake Rd, Colombo',
                'position' => 'coach',
                'date_of_birth' => '1985-05-10',
                'joined_date' => '2023-01-01',
                'salary' => 75000.00,
                'contract_start_date' => '2023-01-01',
                'contract_end_date' => '2024-12-31',
                'status' => 'active',
            ],
            [
                'employee_id' => 'EMP2023002',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'nic' => '198712345678',
                'phone' => '0774567890',
                'whatsapp_number' => '0774567890',
                'address' => '321 Hill St, Galle',
                'position' => 'physiotherapist',
                'date_of_birth' => '1987-08-15',
                'joined_date' => '2023-02-01',
                'salary' => 65000.00,
                'contract_start_date' => '2023-02-01',
                'contract_end_date' => '2024-12-31',
                'status' => 'active',
            ],
        ];

        foreach ($staffMembers as $staffData) {
            // Create user account for staff
            $user = User::create([
                'name' => $staffData['first_name'] . ' ' . $staffData['last_name'],
                'email' => strtolower($staffData['first_name']) . '.' . strtolower($staffData['last_name']) . '@example.com',
                'password' => Hash::make('password'),
            ]);

            // Create staff member with user_id
            $staffData['user_id'] = $user->id;
            Staff::create($staffData);
        }
    }
}
