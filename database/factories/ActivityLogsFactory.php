<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ActivityLogs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ActivityLogs>
 */
final class ActivityLogsFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = ActivityLogs::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'action' => fake()->word,
            'image' => fake()->optional()->word,
            'log' => fake()->text,
            'subject_type' => fake()->word,
            'subject_id' => fake()->randomNumber(),
            'object_type' => fake()->optional()->word,
            'object_id' => fake()->optional()->randomNumber(),
        ];
    }
}
