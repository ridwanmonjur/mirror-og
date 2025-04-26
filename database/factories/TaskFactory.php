<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Task>
 */
final class TaskFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Task::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'task_name' => fake()->word,
            'action_time' => fake()->dateTime(),
            'taskable_type' => fake()->word,
            'taskable_id' => fake()->randomNumber(),
        ];
    }
}
