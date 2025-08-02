<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Discount>
 */
final class DiscountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Discount::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->optional()->name,
            'coupon' => fake()->optional()->word,
            'type' => fake()->optional()->word,
            'amount' => fake()->optional()->word,
            'startDate' => fake()->optional()->date(),
            'endDate' => fake()->optional()->date(),
            'startTime' => fake()->optional()->time(),
            'endTime' => fake()->optional()->time(),
            'isEnforced' => fake()->optional()->randomNumber(1),
        ];
    }
}
