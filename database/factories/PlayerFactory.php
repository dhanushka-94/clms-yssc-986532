<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        $contractStart = $this->faker->dateTimeBetween('-2 years', 'now');
        $contractEnd = $this->faker->dateTimeBetween($contractStart, '+2 years');

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $this->faker->unique()->numerify('############'),
            'ffsl_number' => $this->faker->unique()->numerify('FFSL####'),
            'phone' => $this->faker->phoneNumber,
            'whatsapp_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'position' => $this->faker->randomElement(['striker', 'midfielder', 'defender', 'goalkeeper']),
            'date_of_birth' => $this->faker->date(),
            'joined_date' => $this->faker->date(),
            'contract_amount' => $this->faker->numberBetween(50000, 200000),
            'contract_start_date' => $contractStart,
            'contract_end_date' => $contractEnd,
            'achievements' => $this->faker->text(),
            'status' => $this->faker->randomElement(['active', 'injured', 'suspended', 'inactive']),
        ];
    }
} 