<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventType>
 */
final class EventTypeFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = EventType::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'eventType' => fake()->optional()->word,
            'eventDefinitions' => fake()->optional()->word,
        ];
    }
}
