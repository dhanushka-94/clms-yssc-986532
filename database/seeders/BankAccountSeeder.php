<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankAccounts = [
            [
                'bank_name' => 'Commercial Bank',
                'branch_name' => 'Colombo Main',
                'account_name' => 'YSSC Main Account',
                'account_number' => '1234567890',
                'account_type' => 'current',
                'currency' => 'LKR',
                'initial_balance' => 1000000.00,
                'current_balance' => 1000000.00,
                'status' => 'active',
            ],
            [
                'bank_name' => 'Bank of Ceylon',
                'branch_name' => 'Kandy',
                'account_name' => 'YSSC Operations',
                'account_number' => '0987654321',
                'account_type' => 'savings',
                'currency' => 'LKR',
                'initial_balance' => 500000.00,
                'current_balance' => 500000.00,
                'status' => 'active',
            ],
            [
                'bank_name' => 'Sampath Bank',
                'branch_name' => 'Galle',
                'account_name' => 'YSSC Reserve',
                'account_number' => '5678901234',
                'account_type' => 'savings',
                'currency' => 'USD',
                'initial_balance' => 10000.00,
                'current_balance' => 10000.00,
                'status' => 'active',
            ],
        ];

        foreach ($bankAccounts as $accountData) {
            BankAccount::create($accountData);
        }
    }
}
