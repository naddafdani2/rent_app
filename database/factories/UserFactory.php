<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        // Generate unique phone number
        $phone = $this->faker->unique()->numerify('##########');
        
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->optional(80)->safeEmail(), // 80% chance of having email
            'password' => Hash::make('password123'),
            'phone' => $phone,
            'birth_date' => $this->faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'personal_photo' => ' ',
            'id_photo' => ' ',
            'is_approved' => $this->faker->boolean(80),
            'role' => 'user', // Default is user
            'email_verified_at' => $this->faker->optional(70)->dateTimeBetween('-1 year', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
                'is_approved' => true,
            ];
        });
    }

    // Only admin() method needed, remove owner() method

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => true,
            ];
        });
    }

    public function unapproved()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => false,
            ];
        });
    }
}