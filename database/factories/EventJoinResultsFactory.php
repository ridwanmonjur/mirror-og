<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventJoinResults;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventJoinResults>
 */
final class EventJoinResultsFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = EventJoinResults::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'join_events_id' => fake()->randomNumber(),
            'position' => fake()->randomNumber(),
            'event_id' => \App\Models\EventDetail::factory(),
        ];
    }
}
