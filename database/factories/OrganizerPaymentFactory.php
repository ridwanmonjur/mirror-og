<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrganizerPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\OrganizerPayment>
 */
final class OrganizerPaymentFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = OrganizerPayment::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'payment_amount' => fake()->randomFloat(2, 10, 1000),
            'discount_amount' => fake()->randomFloat(2, 0, 100),
            'user_id' => \App\Models\User::factory(),
            'history_id' => \App\Models\TransactionHistory::factory(),
            'payment_id' => \App\Models\RecordStripe::factory(),
        ];
    }
}