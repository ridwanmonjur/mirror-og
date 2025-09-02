<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Blocks;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Blocks>
 */
final class BlocksFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blocks::class;

    public function definition(): array
    {
        return [];
    }

}
