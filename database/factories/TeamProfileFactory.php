<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TeamProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TeamProfile>
 */
final class TeamProfileFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = TeamProfile::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'backgroundColor' => fake()->optional()->word,
            'backgroundBanner' => fake()->optional()->word,
            'backgroundGradient' => fake()->optional()->word,
            'fontColor' => fake()->optional()->word,
            'frameColor' => fake()->optional()->word,
            'team_id' => \App\Models\Team::factory(),
            'follower_count' => fake()->randomNumber(),
        ];
    }
}
