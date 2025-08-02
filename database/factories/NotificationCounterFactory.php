<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotificationCounter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\NotificationCounter>
 */
final class NotificationCounterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NotificationCounter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'social_count' => fake()->randomNumber(),
            'teams_count' => fake()->randomNumber(),
            'event_count' => fake()->randomNumber(),
        ];
    }
}
