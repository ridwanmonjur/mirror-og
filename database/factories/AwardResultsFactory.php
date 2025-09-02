<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AwardResults;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AwardResults>
 */
final class AwardResultsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AwardResults::class;

    public function definition(): array
    {
        return [];
    }

}
