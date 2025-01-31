<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        $contractStart = $this->faker->dateTimeBetween('-2 years', 'now');
        $contractEnd = $this->faker->dateTimeBetween($contractStart, '+2 years');

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $this->faker->unique()->numerify('############'),
            'phone' => $this->faker->phoneNumber,
            'whatsapp_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'role' => $this->faker->randomElement(['coach', 'assistant_coach', 'physiotherapist', 'manager']),
            'date_of_birth' => $this->faker->date(),
            'joined_date' => $this->faker->date(),
            'contract_start_date' => $contractStart,
            'contract_end_date' => $contractEnd,
            'salary' => $this->faker->numberBetween(30000, 100000),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
} 