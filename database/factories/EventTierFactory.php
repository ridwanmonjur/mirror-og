<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventTier>
 */
final class EventTierFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = EventTier::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'eventTier' => fake()->optional()->word,
            'tierIcon' => fake()->optional()->word,
            'tierTeamSlot' => fake()->optional()->word,
            'tierPrizePool' => fake()->optional()->word,
            'tierEntryFee' => fake()->optional()->word,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
