<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Stars;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Stars>
 */
final class StarsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stars::class;

    public function definition(): array
    {
        return [];
    }

}
