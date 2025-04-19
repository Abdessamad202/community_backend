<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $created_at = fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s');

        return [
            'post_id'=> fake()->numberBetween(1,200),
            'user_id'=> fake()->numberBetween(1,200),
            //
            'content' => fake()->paragraph(),
            'created_at' => $created_at,
            'updated_at'=> $created_at
        ];
    }
}
