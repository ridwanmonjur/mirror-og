<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TransactionHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TransactionHistory>
 */
final class TransactionHistoryFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = TransactionHistory::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['payment', 'withdrawal', 'refund', 'deposit']),
            'link' => fake()->optional()->url(),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'summary' => fake()->sentence(),
            'isPositive' => fake()->boolean(),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}