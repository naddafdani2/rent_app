<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        $cities = ['Jaramana', 'Hamidieh', 'Azizieh', 'Alwadi', 'Cornish', 'Alsaha', 'Alsouq', 'Alakaber','Alaasi','Alsahel'];
        $states = ['Damascus', 'Aleppo', 'Latakia', 'Homs', 'Hama','Rif_Dimashq','Idlib','Hasaka','Dir_Ezzor','Daraa','Sweyda','Tartous'];
        $title = ['Villa','House','Studio',];
        
        return [
            'title' => $this->faker->randomElement($title),
            'description' => $this->faker->paragraphs(2, true),
            'total_area' => (string) $this->faker->numberBetween(50, 300),
            'price_per_day' => $this->faker->randomFloat(2, 50, 500),
            'price_per_month' => $this->faker->randomFloat(2, 1500, 8000),
            'state' => $this->faker->randomElement($states),
            'city' => $this->faker->randomElement($cities),
            'street' => $this->faker->streetName(),
            'building_number' => $this->faker->buildingNumber(),
            'level' => (string) $this->faker->numberBetween(1, 5),
            'is_available' => $this->faker->boolean(80),
            'owner_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_available' => true,
            ];
        });
    }

    public function unavailable()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_available' => false,
            ];
        });
    }
}