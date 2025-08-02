<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\UserProfile>
 */
final class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'backgroundColor' => fake()->optional()->word,
            'backgroundBanner' => fake()->optional()->word,
            'backgroundGradient' => fake()->optional()->word,
            'fontColor' => fake()->optional()->word,
            'frameColor' => fake()->optional()->word,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
