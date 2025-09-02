<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TeamFollow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TeamFollow>
 */
final class TeamFollowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamFollow::class;

    public function definition(): array
    {
        return [];
    }

}
