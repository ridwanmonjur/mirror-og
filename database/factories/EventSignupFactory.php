<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventSignup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventSignup>
 */
final class EventSignupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventSignup::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'event_id' => \App\Models\EventDetail::factory(),
            'signup_open' => fake()->dateTime(),
            'normal_signup_start_advanced_close' => fake()->dateTime(),
            'signup_close' => fake()->dateTime(),
        ];
    }
}
