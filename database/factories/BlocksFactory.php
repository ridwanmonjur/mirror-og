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

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'blocked_user_id' => \App\Models\User::factory(),
        ];
    }
}
