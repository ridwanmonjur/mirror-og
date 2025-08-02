<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BracketDeadlineSetup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BracketDeadlineSetup>
 */
final class BracketDeadlineSetupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BracketDeadlineSetup::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tier_id' => \App\Models\EventTier::factory(),
            'deadline_config' => fake()->word,
        ];
    }
}
