<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RecordStripe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\RecordStripe>
 */
final class RecordStripeFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = RecordStripe::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'payment_id' => fake()->optional()->word,
            'payment_status' => 'SUCCEEDED',
            'payment_amount' => fake()->word,
            // 'user_id' => \App\Models\User::factory()
        ];
    }
}
