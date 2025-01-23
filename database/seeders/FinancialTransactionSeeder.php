<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\BankAccount;
use Carbon\Carbon;

class FinancialTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all members, players, sponsors, and bank accounts
        $members = Member::all();
        $players = Player::all();
        $sponsors = Sponsor::all();
        $bankAccount = BankAccount::first();

        // Create membership fee payments for each member
        foreach ($members as $member) {
            FinancialTransaction::create([
                'transaction_number' => 'TRX' . date('Y') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'transaction_date' => $member->joined_date,
                'date' => $member->joined_date,
                'type' => 'income',
                'category' => 'membership_fee',
                'amount' => 5000.00,
                'description' => 'Membership fee payment',
                'payment_method' => 'cash',
                'status' => 'completed',
                'transactionable_type' => Member::class,
                'transactionable_id' => $member->id,
                'bank_account_id' => $bankAccount->id,
            ]);
        }

        // Create monthly salary payments for each player
        foreach ($players as $player) {
            FinancialTransaction::create([
                'transaction_number' => 'TRX' . date('Y') . str_pad(rand(1000, 1999), 3, '0', STR_PAD_LEFT),
                'transaction_date' => now()->subMonth(),
                'date' => now()->subMonth(),
                'type' => 'expense',
                'category' => 'salary',
                'amount' => $player->contract_amount,
                'description' => 'Monthly salary payment',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'transactionable_type' => Player::class,
                'transactionable_id' => $player->id,
                'bank_account_id' => $bankAccount->id,
            ]);
        }

        // Create sponsorship payments for each sponsor
        foreach ($sponsors as $sponsor) {
            FinancialTransaction::create([
                'transaction_number' => 'TRX' . date('Y') . str_pad(rand(2000, 2999), 3, '0', STR_PAD_LEFT),
                'transaction_date' => $sponsor->sponsorship_start_date,
                'date' => $sponsor->sponsorship_start_date,
                'type' => 'income',
                'category' => 'sponsorship',
                'amount' => $sponsor->sponsorship_amount,
                'description' => 'Sponsorship payment',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'transactionable_type' => Sponsor::class,
                'transactionable_id' => $sponsor->id,
                'bank_account_id' => $bankAccount->id,
            ]);
        }

        // Create some general expenses
        $expenses = [
            [
                'transaction_number' => 'TRX' . date('Y') . str_pad(rand(3000, 3999), 3, '0', STR_PAD_LEFT),
                'transaction_date' => now()->subDays(15),
                'date' => now()->subDays(15),
                'type' => 'expense',
                'category' => 'maintenance',
                'amount' => 25000.00,
                'description' => 'Ground maintenance',
                'payment_method' => 'cash',
                'status' => 'completed',
                'transactionable_type' => BankAccount::class,
                'transactionable_id' => $bankAccount->id,
                'bank_account_id' => $bankAccount->id,
            ],
            [
                'transaction_number' => 'TRX' . date('Y') . str_pad(rand(4000, 4999), 3, '0', STR_PAD_LEFT),
                'transaction_date' => now()->subDays(7),
                'date' => now()->subDays(7),
                'type' => 'expense',
                'category' => 'equipment',
                'amount' => 15000.00,
                'description' => 'Equipment purchase',
                'payment_method' => 'cash',
                'status' => 'completed',
                'transactionable_type' => BankAccount::class,
                'transactionable_id' => $bankAccount->id,
                'bank_account_id' => $bankAccount->id,
            ],
        ];

        foreach ($expenses as $expense) {
            FinancialTransaction::create($expense);
        }
    }
}
