<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Apartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional(80)->paragraph(), // 80% chance of having a comment
            'user_id' => User::factory(),
            'apartment_id' => Apartment::factory(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }

    public function positive()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(4, 5),
                'comment' => $this->faker->sentence(),
            ];
        });
    }

    public function negative()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(1, 2),
                'comment' => $this->faker->sentence(),
            ];
        });
    }
}