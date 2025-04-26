<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrganizerFollow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\OrganizerFollow>
 */
final class OrganizerFollowFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = OrganizerFollow::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'participant_user_id' => \App\Models\User::factory(),
            'organizer_user_id' => \App\Models\User::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
