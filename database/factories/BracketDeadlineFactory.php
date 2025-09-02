<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BracketDeadline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BracketDeadline>
 */
final class BracketDeadlineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BracketDeadline::class;

    public function definition(): array
    {
        return [];
    }

   
}
