<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TeamProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TeamProfile>
 */
final class TeamProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamProfile::class;

    public function definition(): array
    {
        return [];
    }

}
