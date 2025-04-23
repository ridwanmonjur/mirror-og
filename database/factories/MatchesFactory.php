<?php
namespace Database\Factories;

use App\Models\Matches;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchesFactory extends Factory
{
    protected $model = Matches::class;

    public function definition(): array
    {
        return [
            'order' => $this->faker->numberBetween(0, 5),
            'team1_id' => null,
            'team2_id' => null,
            'event_details_id' => 52,
            'team1_position' => $this->faker->regexify('[A-Z][0-9]?'),
            'team2_position' => $this->faker->regexify('[A-Z][0-9]?'),
            'stage_name' => $this->faker->randomElement(['F', 'W', 'U', 'L']),
            'inner_stage_name' => $this->faker->randomElement(['e1', 'e2', 'p1', 'p2']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
