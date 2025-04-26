<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TeamCaptain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TeamCaptain>
 */
final class TeamCaptainFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = TeamCaptain::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'team_member_id' => \App\Models\User::factory(),
            'teams_id' => \App\Models\Team::factory(),
        ];
    }
}
