<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Friend;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Friend>
 */
final class FriendFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Friend::class;

    public function definition(): array
    {
        return [];
    }

   
}
