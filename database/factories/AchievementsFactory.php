<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Achievements;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Achievements>
 */
final class AchievementsFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Achievements::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'title' => fake()->title,
            'description' => fake()->text,
            'join_event_id' => fake()->randomNumber(),
        ];
    }
}
