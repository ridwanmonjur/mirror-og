<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RosterMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\RosterMember>
 */
final class RosterMemberFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = RosterMember::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'join_events_id' => fake()->randomNumber(),
            'team_member_id' => fake()->randomNumber(),
            'team_id' => \App\Models\Team::factory(),
            'vote_to_quit' => fake()->optional()->randomNumber(1),
        ];
    }
}
