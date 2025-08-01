<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ImageVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ImageVideo>
 */
final class ImageVideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ImageVideo::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'file_path' => fake()->word,
            'file_type' => fake()->randomElement(['image', 'video']),
            'mime_type' => fake()->word,
            'size' => fake()->randomNumber(),
        ];
    }
}
