<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ParticipantPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ParticipantPayment>
 */
final class ParticipantPaymentFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = ParticipantPayment::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'team_members_id' => fake()->randomNumber(),
            'join_events_id' => fake()->randomNumber(),
            'user_id' => fake()->randomNumber(),
            'payment_id' => \App\Models\PaymentTransaction::factory(),
            'payment_amount' => fake()->optional()->word,
            'members_id' => \App\Models\TeamMember::factory(),
        ];
    }
}
