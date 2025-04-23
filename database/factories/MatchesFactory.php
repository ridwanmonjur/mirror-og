<?php
namespace Database\Factories;

use App\Models\EventDetail;
use App\Models\Matches;
use App\Models\Team;

use Illuminate\Database\Eloquent\Factories\Factory;

class MatchesFactory extends Factory
{
    protected $model = Matches::class;

    public function definition(): array
    {
        return [
            'order' => $this->faker->numberBetween(0, 5),
            'team1_id' => Team::factory(),
            'team2_id' => Team::factory(),
            'event_details_id' => EventDetail::factory(),
            'team1_position' => $this->faker->regexify('[A-Z][0-9]?'),
            'team2_position' => $this->faker->regexify('[A-Z][0-9]?'),
            'stage_name' => $this->faker->randomElement(['F', 'W', 'U', 'L']),
            'inner_stage_name' => $this->faker->randomElement(['e1', 'e2', 'p1', 'p2']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
