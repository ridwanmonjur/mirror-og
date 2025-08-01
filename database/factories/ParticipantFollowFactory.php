<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ParticipantFollow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ParticipantFollow>
 */
final class ParticipantFollowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ParticipantFollow::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'participant_follower' => \App\Models\User::factory(),
            'participant_followee' => \App\Models\User::factory(),
        ];
    }
}
