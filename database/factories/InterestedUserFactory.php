<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\InterestedUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\InterestedUser>
 */
final class InterestedUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InterestedUser::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'email' => \App\Models\User::factory(),
            'email_verified_at' => fake()->optional()->datetime(),
            'email_verified_token' => fake()->optional()->word,
            'pass_text' => fake()->optional()->word,
        ];
    }
}
