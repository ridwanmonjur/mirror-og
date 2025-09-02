<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RosterMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\RosterMember>
 */
final class RosterMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RosterMember::class;

    public function definition(): array
    {
        return [];
    }

  
}
