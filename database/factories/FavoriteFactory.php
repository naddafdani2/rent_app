<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Apartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'apartment_id' => Apartment::factory(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}