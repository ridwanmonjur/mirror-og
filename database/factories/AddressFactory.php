<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Address>
 */
final class AddressFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Address::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'city' => fake()->city,
            'addressLine1' => fake()->word,
            'addressLine2' => fake()->optional()->word,
            'postcode' => fake()->postcode,
            'country' => fake()->country,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
