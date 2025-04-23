<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Award;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Award>
 */
final class AwardFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Award::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'title' => fake()->optional()->title,
            'image' => fake()->optional()->word,
            'description' => fake()->optional()->text,
        ];
    }
}
