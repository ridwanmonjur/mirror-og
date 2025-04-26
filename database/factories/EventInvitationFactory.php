<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventInvitation>
 */
final class EventInvitationFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = EventInvitation::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'organizer_user_id' => \App\Models\User::factory(),
            'event_id' => \App\Models\EventDetail::factory(),
            'participant_user_id' => \App\Models\User::factory(),
            'team_id' => \App\Models\Team::factory(),
        ];
    }
}
