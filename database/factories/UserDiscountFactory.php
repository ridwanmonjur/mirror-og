<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\UserDiscount>
 */
final class UserDiscountFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = UserDiscount::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'amount' => fake()->optional()->word,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
