<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $this->faker->unique()->numerify('############'),
            'phone' => $this->faker->phoneNumber,
            'whatsapp_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'joined_date' => $this->faker->date(),
            'membership_type' => 'regular',
            'designation' => $this->faker->jobTitle,
            'membership_fee' => $this->faker->randomFloat(2, 1000, 10000),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
} 