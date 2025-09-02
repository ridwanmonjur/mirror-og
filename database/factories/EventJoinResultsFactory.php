<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventJoinResults;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventJoinResults>
 */
final class EventJoinResultsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventJoinResults::class;

    public function definition(): array
    {
        return [];
    }

}
