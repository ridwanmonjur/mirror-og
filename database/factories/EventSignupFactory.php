<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventSignup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EventSignup>
 */
final class EventSignupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventSignup::class;

    public function definition(): array
    {
        return [];
    }

}
