<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BracketDeadlineSetup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BracketDeadlineSetup>
 */
final class BracketDeadlineSetupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BracketDeadlineSetup::class;

    public function definition(): array
    {
        return [];
    }

}
