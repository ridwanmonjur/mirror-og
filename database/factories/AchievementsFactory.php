<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Achievements;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Achievements>
 */
final class AchievementsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievements::class;

    public function definition(): array
    {
        return [];
    }

    
}
