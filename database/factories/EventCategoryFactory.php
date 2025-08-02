<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventCategory>
 */
final class EventCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'gameTitle' => fake()->optional()->word,
            'gameIcon' => fake()->optional()->word,
            'eventDefinitions' => fake()->optional()->word,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
