<?php

namespace Database\Factories;

use App\Models\Apartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class Apt_imageFactory extends Factory
{
    public function definition(): array
    {
        // Using Faker's image URLs (you can replace with local images later)
        $imageTypes = ['living-room', 'bedroom', 'kitchen', 'bathroom' , 'garden'];
        $type = $this->faker->randomElement($imageTypes);
        
        return [
            'image_path' => 'apartments/' . $this->faker->uuid() . '.jpg',
            'is_primary' => $this->faker->boolean(20), // 20% chance to be primary
            'apartment_id' => Apartment::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function primary()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => true,
            ];
        });
    }

    public function secondary()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => false,
            ];
        });
    }
}