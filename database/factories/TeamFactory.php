<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<\App\Models\Team>
 */
final class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    public function definition(): array
    {
        return [];
    }

    

    public static function deleteRelatedTables()
    {

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables
        DB::table('teams')->truncate();
        DB::table('team_profile')->truncate();
        DB::table('team_follows')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
