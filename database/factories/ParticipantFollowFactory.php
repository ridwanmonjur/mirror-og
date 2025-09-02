<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ParticipantFollow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ParticipantFollow>
 */
final class ParticipantFollowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ParticipantFollow::class;

    public function definition(): array
    {
        return [];
    }

}
