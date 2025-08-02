<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventTierSignup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventTierSignup>
 */
final class EventTierSignupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventTierSignup::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tier_id' => \App\Models\EventTier::factory(),
            'type_id' => \App\Models\EventType::factory(),
            'signup_open' => fake()->randomNumber(),
            'signup_close' => fake()->randomNumber(),
            'normal_signup_start_advanced_close' => fake()->randomNumber(),
        ];
    }
}
