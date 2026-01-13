<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Apartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate = clone $startDate;
        $endDate->modify('+' . $this->faker->numberBetween(2, 14) . ' days');
        
        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => $this->faker->randomElement(['accepted', 'modified', 'cancelled']),
            'user_id' => User::factory(),
            'apartments_id' => Apartment::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }

    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted',
            ];
        });
    }

    public function upcoming()
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('+2 days', '+1 month');
            $endDate = clone $startDate;
            $endDate->modify('+' . $this->faker->numberBetween(2, 7) . ' days');
            
            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'accepted',
            ];
        });
    }

    public function past()
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-6 months', '-1 month');
            $endDate = clone $startDate;
            $endDate->modify('+' . $this->faker->numberBetween(2, 14) . ' days');
            
            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'accepted',
            ];
        });
    }
}