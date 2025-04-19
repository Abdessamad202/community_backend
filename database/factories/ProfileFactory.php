<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a date of birth between 18 and 30 years ago
        $date_of_birth = fake()->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d');

        // Generate created_at between 2 years ago and now

        return [
            'user_id' => fake()->numberBetween(1, 200), // Random user ID between 1 and 200
            'name' => fake()->firstName(), // Generate a random first name
            'date_of_birth' => $date_of_birth, // Assign the generated birth date
            'gender' => fake()->randomElement(['M', 'F']), // Randomly select 'M' or 'F'
            'picture' => fake()->imageUrl(), // Generate a random profile picture URL
            'description' => fake()->text(), // Generate a random text for description
        ];
    }
}
