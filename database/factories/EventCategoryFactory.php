<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventCategory>
 */
final class EventCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventCategory::class;

    public function definition(): array
    {
        return [];
    }

}
