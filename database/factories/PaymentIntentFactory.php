<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PaymentIntent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\PaymentIntent>
 */
final class PaymentIntentFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = PaymentIntent::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomNumber(),
            'payment_intent_id' => fake()->word,
            'customer_id' => fake()->word,
            'status' => fake()->word,
            'amount' => fake()->randomNumber(),
        ];
    }
}
