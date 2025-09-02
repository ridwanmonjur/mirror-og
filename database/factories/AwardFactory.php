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

    public function definition(): array
    {
        return [];
    }

}
