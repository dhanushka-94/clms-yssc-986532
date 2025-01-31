<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\BankAccount;
use App\Models\Attendance;
use App\Models\Player;
use App\Models\Staff;
use App\Models\Member;
use App\Models\Sponsor;
use App\Models\FinancialTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample members (20 members)
        $members = [
            ['name' => 'John Smith', 'email' => 'john@example.com', 'role' => 'member'],
            ['name' => 'Sarah Wilson', 'email' => 'sarah@example.com', 'role' => 'member'],
            ['name' => 'Michael Brown', 'email' => 'michael@example.com', 'role' => 'member'],
            ['name' => 'Emma Davis', 'email' => 'emma@example.com', 'role' => 'member'],
            ['name' => 'James Johnson', 'email' => 'james@example.com', 'role' => 'member'],
            ['name' => 'William Turner', 'email' => 'william@example.com', 'role' => 'member'],
            ['name' => 'Oliver White', 'email' => 'oliver@example.com', 'role' => 'member'],
            ['name' => 'Sophia Martinez', 'email' => 'sophia@example.com', 'role' => 'member'],
            ['name' => 'Lucas Anderson', 'email' => 'lucas@example.com', 'role' => 'member'],
            ['name' => 'Isabella Thomas', 'email' => 'isabella@example.com', 'role' => 'member'],
            ['name' => 'Mason Garcia', 'email' => 'mason@example.com', 'role' => 'member'],
            ['name' => 'Ava Robinson', 'email' => 'ava@example.com', 'role' => 'member'],
            ['name' => 'Ethan Clark', 'email' => 'ethan@example.com', 'role' => 'member'],
            ['name' => 'Amelia Walker', 'email' => 'amelia@example.com', 'role' => 'member'],
            ['name' => 'Alexander Hall', 'email' => 'alexander@example.com', 'role' => 'member'],
            ['name' => 'Mia Young', 'email' => 'mia@example.com', 'role' => 'member'],
            ['name' => 'Daniel King', 'email' => 'daniel@example.com', 'role' => 'member'],
            ['name' => 'Charlotte Lee', 'email' => 'charlotte@example.com', 'role' => 'member'],
            ['name' => 'Henry Wright', 'email' => 'henry@example.com', 'role' => 'member'],
            ['name' => 'Victoria Scott', 'email' => 'victoria@example.com', 'role' => 'member'],
        ];

        foreach ($members as $member) {
            $user = User::create([
                'name' => $member['name'],
                'email' => $member['email'],
                'password' => Hash::make('password'),
                'role' => $member['role'],
            ]);

            // Create member record
            $nameParts = explode(' ', $member['name']);
            Member::create([
                'user_id' => $user->id,
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1],
                'membership_number' => 'M' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'nic' => rand(100000000, 999999999) . 'V',
                'phone' => '+94' . rand(700000000, 799999999),
                'whatsapp_number' => '+94' . rand(700000000, 799999999),
                'address' => fake()->address,
                'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                'joined_date' => now()->subMonths(rand(1, 24))->format('Y-m-d'),
                'membership_type' => fake()->randomElement(['regular', 'premium', 'lifetime']),
                'status' => 'active',
            ]);

            // Create player record for some members (15 players)
            if ($user->id <= 15) {
                Player::create([
                    'user_id' => $user->id,
                    'player_id' => 'P' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1],
                    'nic' => rand(100000000, 999999999) . 'V',
                    'phone' => '+94' . rand(700000000, 799999999),
                    'address' => fake()->address,
                    'date_of_birth' => fake()->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
                    'joined_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                    'position' => fake()->randomElement(['Forward', 'Midfielder', 'Defender', 'Goalkeeper']),
                    'contract_amount' => rand(50000, 150000),
                    'contract_start_date' => now()->format('Y-m-d'),
                    'contract_end_date' => now()->addYears(2)->format('Y-m-d'),
                    'status' => fake()->randomElement(['active', 'injured', 'suspended']),
                    'ffsl_number' => 'FFSL' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'whatsapp_number' => '+94' . rand(700000000, 799999999),
                ]);
            }
        }

        // Create sample staff (10 staff members)
        $staffMembers = [
            ['name' => 'David Lee', 'email' => 'david@staff.com', 'role' => 'staff', 'position' => 'Head Coach'],
            ['name' => 'Lisa Chen', 'email' => 'lisa@staff.com', 'role' => 'staff', 'position' => 'Assistant Coach'],
            ['name' => 'Robert Taylor', 'email' => 'robert@staff.com', 'role' => 'staff', 'position' => 'Physiotherapist'],
            ['name' => 'Maria Rodriguez', 'email' => 'maria@staff.com', 'role' => 'staff', 'position' => 'Team Doctor'],
            ['name' => 'Kevin Wilson', 'email' => 'kevin@staff.com', 'role' => 'staff', 'position' => 'Fitness Trainer'],
            ['name' => 'Rachel Green', 'email' => 'rachel@staff.com', 'role' => 'staff', 'position' => 'Nutritionist'],
            ['name' => 'Thomas Brown', 'email' => 'thomas@staff.com', 'role' => 'staff', 'position' => 'Equipment Manager'],
            ['name' => 'Jennifer White', 'email' => 'jennifer@staff.com', 'role' => 'staff', 'position' => 'Team Coordinator'],
            ['name' => 'Andrew Clark', 'email' => 'andrew@staff.com', 'role' => 'staff', 'position' => 'Youth Coach'],
            ['name' => 'Michelle Lee', 'email' => 'michelle@staff.com', 'role' => 'staff', 'position' => 'Administrative Assistant'],
        ];

        foreach ($staffMembers as $staffMember) {
            $user = User::create([
                'name' => $staffMember['name'],
                'email' => $staffMember['email'],
                'password' => Hash::make('password'),
                'role' => $staffMember['role'],
            ]);

            $nameParts = explode(' ', $staffMember['name']);
            Staff::create([
                'user_id' => $user->id,
                'employee_id' => 'E' . str_pad($user->id - 20, 4, '0', STR_PAD_LEFT),
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1],
                'position' => $staffMember['position'],
                'nic' => rand(100000000, 999999999) . 'V',
                'phone' => '+94' . rand(700000000, 799999999),
                'whatsapp_number' => '+94' . rand(700000000, 799999999),
                'address' => fake()->address,
                'date_of_birth' => fake()->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
                'joined_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                'salary' => rand(50000, 150000),
                'contract_start_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                'contract_end_date' => now()->addYears(2)->format('Y-m-d'),
                'status' => 'active',
            ]);
        }

        // Create sample sponsors (8 sponsors)
        $sponsors = [
            ['name' => 'SportsTech Inc.', 'type' => 'main'],
            ['name' => 'Global Athletics', 'type' => 'co'],
            ['name' => 'Power Nutrition', 'type' => 'other'],
            ['name' => 'Elite Sportswear', 'type' => 'main'],
            ['name' => 'Victory Equipment', 'type' => 'co'],
            ['name' => 'Champions Bank', 'type' => 'main'],
            ['name' => 'Sports Medicine Plus', 'type' => 'other'],
            ['name' => 'Pro Fitness Gear', 'type' => 'co'],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::create([
                'company_name' => $sponsor['name'],
                'sponsor_id' => 'S' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'contact_person' => fake()->name,
                'email' => strtolower(str_replace(' ', '', $sponsor['name'])) . '@example.com',
                'phone' => '+94' . rand(700000000, 799999999),
                'whatsapp_number' => '+94' . rand(700000000, 799999999),
                'address' => fake()->address,
                'sponsorship_type' => $sponsor['type'],
                'sponsorship_amount' => rand(100000, 500000),
                'sponsorship_start_date' => now()->subMonths(rand(1, 6))->format('Y-m-d'),
                'sponsorship_end_date' => now()->addYears(1)->format('Y-m-d'),
                'status' => 'active',
                'terms_and_conditions' => fake()->paragraphs(3, true),
            ]);
        }

        // Create sample bank accounts (5 accounts)
        $bankAccounts = [
            [
                'account_name' => 'Main Operating Account',
                'account_number' => '1234567890',
                'bank_name' => 'Bank of Example',
                'branch_name' => 'Main Branch',
                'swift_code' => 'BEXUS123',
                'current_balance' => 10000.00,
                'initial_balance' => 10000.00,
                'account_type' => 'current',
                'status' => 'active',
                'currency' => 'USD',
            ],
            [
                'account_name' => 'Events Fund',
                'account_number' => '0987654321',
                'bank_name' => 'City Bank',
                'branch_name' => 'Downtown Branch',
                'swift_code' => 'CITUS456',
                'current_balance' => 5000.00,
                'initial_balance' => 5000.00,
                'account_type' => 'savings',
                'status' => 'active',
                'currency' => 'USD',
            ],
            [
                'account_name' => 'Emergency Reserve',
                'account_number' => '5555555555',
                'bank_name' => 'National Bank',
                'branch_name' => 'Central Branch',
                'swift_code' => 'NATUS789',
                'current_balance' => 15000.00,
                'initial_balance' => 15000.00,
                'account_type' => 'savings',
                'status' => 'active',
                'currency' => 'USD',
            ],
            [
                'account_name' => 'Sponsorship Account',
                'account_number' => '7777777777',
                'bank_name' => 'Global Bank',
                'branch_name' => 'Corporate Branch',
                'swift_code' => 'GLBUS321',
                'current_balance' => 25000.00,
                'initial_balance' => 25000.00,
                'account_type' => 'current',
                'status' => 'active',
                'currency' => 'USD',
            ],
            [
                'account_name' => 'Player Salaries Account',
                'account_number' => '9999999999',
                'bank_name' => 'Metro Bank',
                'branch_name' => 'Sports Complex Branch',
                'swift_code' => 'MTRUS654',
                'current_balance' => 35000.00,
                'initial_balance' => 35000.00,
                'account_type' => 'current',
                'status' => 'active',
                'currency' => 'USD',
            ],
        ];

        foreach ($bankAccounts as $account) {
            BankAccount::create($account);
        }

        // Create sample financial transactions (30 transactions)
        $transactionTypes = [
            ['description' => 'Monthly Membership Fee', 'type' => 'income', 'category' => 'membership', 'amount' => 500.00],
            ['description' => 'Equipment Purchase', 'type' => 'expense', 'category' => 'equipment', 'amount' => 1200.00],
            ['description' => 'Event Sponsorship', 'type' => 'income', 'category' => 'sponsorship', 'amount' => 2500.00],
            ['description' => 'Utility Bills', 'type' => 'expense', 'category' => 'utilities', 'amount' => 300.00],
            ['description' => 'Workshop Income', 'type' => 'income', 'category' => 'events', 'amount' => 1500.00],
            ['description' => 'Annual Insurance', 'type' => 'expense', 'category' => 'insurance', 'amount' => 2000.00],
            ['description' => 'Tournament Registration Fees', 'type' => 'income', 'category' => 'events', 'amount' => 3000.00],
            ['description' => 'Staff Training', 'type' => 'expense', 'category' => 'training', 'amount' => 800.00],
            ['description' => 'Player Salary', 'type' => 'expense', 'category' => 'salary', 'amount' => 2500.00],
            ['description' => 'Facility Rental', 'type' => 'expense', 'category' => 'facilities', 'amount' => 1500.00],
        ];

        for ($i = 0; $i < 30; $i++) {
            $transaction = fake()->randomElement($transactionTypes);
            FinancialTransaction::create([
                'description' => $transaction['description'],
                'amount' => $transaction['amount'] * (0.8 + (rand(0, 40) / 100)), // Random variation in amount
                'type' => $transaction['type'],
                'category' => $transaction['category'],
                'transaction_date' => now()->subDays(rand(1, 90))->format('Y-m-d'),
                'bank_account_id' => rand(1, 5),
                'transaction_number' => 'TRX' . Str::random(8),
                'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'check', 'online']),
                'status' => 'completed',
                'transactionable_type' => 'App\\Models\\User',
                'transactionable_id' => rand(1, 20),
            ]);
        }

        // Create sample events (15 events)
        $eventTypes = [
            ['title' => 'Training Session', 'type' => 'practice', 'duration' => 3],
            ['title' => 'Friendly Match', 'type' => 'match', 'duration' => 4],
            ['title' => 'Tournament Game', 'type' => 'match', 'duration' => 5],
            ['title' => 'Fitness Workshop', 'type' => 'practice', 'duration' => 2],
            ['title' => 'Team Meeting', 'type' => 'meeting', 'duration' => 1],
        ];

        $locations = [
            'Main Stadium',
            'Training Ground A',
            'Training Ground B',
            'Indoor Sports Complex',
            'Fitness Center',
            'Conference Room',
        ];

        for ($i = 0; $i < 15; $i++) {
            $eventType = fake()->randomElement($eventTypes);
            $startDate = now()->addDays(rand(1, 60));
            
            $event = Event::create([
                'title' => $eventType['title'] . ' #' . ($i + 1),
                'description' => fake()->sentence(10),
                'type' => $eventType['type'],
                'start_time' => $startDate,
                'end_time' => $startDate->copy()->addHours($eventType['duration']),
                'location' => fake()->randomElement($locations),
                'opponent' => $eventType['type'] === 'match' ? fake()->company . ' FC' : null,
                'venue' => $eventType['type'] === 'match' ? fake()->randomElement(['home', 'away', 'neutral']) : null,
                'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed']),
            ]);

            // Create attendances for this event (random number of attendees)
            $players = Player::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($players as $player) {
                Attendance::create([
                    'event_id' => $event->id,
                    'attendee_type' => 'App\\Models\\Player',
                    'attendee_id' => $player->id,
                    'status' => fake()->randomElement(['present', 'absent', 'late', 'excused']),
                    'check_in_time' => fake()->dateTimeBetween('-2 hours', '+2 hours')->format('H:i:s'),
                ]);
            }
        }
    }
} 