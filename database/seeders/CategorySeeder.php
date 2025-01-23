<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Sponsor Categories
        $sponsorCategories = [
            ['name' => 'Platinum', 'color' => '#E5E4E2'],
            ['name' => 'Gold', 'color' => '#FFD700'],
            ['name' => 'Silver', 'color' => '#C0C0C0'],
            ['name' => 'Bronze', 'color' => '#CD7F32'],
        ];

        foreach ($sponsorCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'sponsor',
                'description' => $category['name'] . ' level sponsor category',
                'color' => $category['color'],
                'is_active' => true,
            ]);
        }

        // Income Categories
        $incomeCategories = [
            ['name' => 'Membership Fees', 'color' => '#4CAF50'],
            ['name' => 'Event Registration', 'color' => '#2196F3'],
            ['name' => 'Sponsorship', 'color' => '#9C27B0'],
            ['name' => 'Donations', 'color' => '#FF9800'],
            ['name' => 'Merchandise Sales', 'color' => '#795548'],
        ];

        foreach ($incomeCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'income',
                'description' => 'Income from ' . strtolower($category['name']),
                'color' => $category['color'],
                'is_active' => true,
            ]);
        }

        // Expense Categories
        $expenseCategories = [
            ['name' => 'Equipment', 'color' => '#F44336'],
            ['name' => 'Venue Rental', 'color' => '#673AB7'],
            ['name' => 'Transportation', 'color' => '#009688'],
            ['name' => 'Marketing', 'color' => '#FF5722'],
            ['name' => 'Staff Salaries', 'color' => '#607D8B'],
            ['name' => 'Utilities', 'color' => '#795548'],
            ['name' => 'Maintenance', 'color' => '#9E9E9E'],
        ];

        foreach ($expenseCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'expense',
                'description' => 'Expenses for ' . strtolower($category['name']),
                'color' => $category['color'],
                'is_active' => true,
            ]);
        }
    }
} 