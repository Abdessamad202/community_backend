<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $createdAt = $this->faker->dateTimeBetween($user->created_at, 'now')->format('Y-m-d H:i:s');

        return [
            'user_id' => $user->id,
            'content' => $this->faker->sentence(),
            'image' => fake()->imageUrl(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
