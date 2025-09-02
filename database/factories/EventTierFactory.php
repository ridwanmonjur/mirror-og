<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventTier>
 */
final class EventTierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventTier::class;

    public function definition(): array
    {
        return [];
    }

}
