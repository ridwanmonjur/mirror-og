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

    public function definition(): array
    {
        return [];
    }

}
