<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Organizer>
 */
final class OrganizerFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Organizer::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'companyName' => fake()->optional()->word,
            'companyDescription' => fake()->optional()->word,
            'user_id' => \App\Models\User::factory(),
            'stripe_customer_id' => fake()->optional()->word,
            'industry' => fake()->optional()->word,
            'type' => fake()->optional()->word,
            'website_link' => fake()->optional()->word,
            'instagram_link' => fake()->optional()->word,
            'facebook_link' => fake()->optional()->word,
            'twitter_link' => fake()->optional()->word,
        ];
    }
}
