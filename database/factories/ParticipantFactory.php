<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Participant>
 */
final class ParticipantFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Participant::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'nickname' => fake()->optional()->word,
            'domain' => fake()->optional()->domainName,
            'bio' => fake()->optional()->text,
            'age' => fake()->optional()->randomNumber(),
            'region' => fake()->optional()->state,
            'birthday' => fake()->optional()->date(),
            'games_data' => fake()->optional()->word,
            'region_name' => fake()->word,
            'region_flag' => fake()->word,
            'isAgeVisible' => fake()->randomNumber(1),
        ];
    }
}
