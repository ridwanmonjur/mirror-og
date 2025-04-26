<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Friend;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Friend>
 */
final class FriendFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Friend::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'user1_id' => \App\Models\User::factory(),
            'user2_id' => \App\Models\User::factory(),
            'actor_id' => fake()->randomNumber(),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected', 'left']),
        ];
    }
}
