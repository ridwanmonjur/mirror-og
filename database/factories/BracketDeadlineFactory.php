<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BracketDeadline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BracketDeadline>
 */
final class BracketDeadlineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BracketDeadline::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stage' => fake()->word,
            'inner_stage' => fake()->word,
            'start_date' => fake()->dateTime(),
            'end_date' => fake()->dateTime(),
            'created_at' => fake()->dateTime(),
            'event_details_id' => \App\Models\EventDetail::factory(),
        ];
    }
}
