<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RosterCaptain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\RosterCaptain>
 */
final class RosterCaptainFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RosterCaptain::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
        ];
    }
}
