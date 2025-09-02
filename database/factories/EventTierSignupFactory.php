<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventTierSignup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventTierSignup>
 */
final class EventTierSignupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventTierSignup::class;

    public function definition(): array
    {
        return [];
    }

}
