<?php
namespace Tests\Unit;

use App\Models\Matches;
use App\Models\BracketDeadline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class DeadlineTasksTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables
        DB::table('users')->truncate();
        DB::table('organizers')->truncate();
        DB::table('participants')->truncate();
        DB::table('notification_counters')->truncate();
        DB::table('user_address')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

    /** @test */
    public function it_equalizes_missing_scores_correctly()
    {
        $trait = new class {
            use \App\Console\Commands\DeadlineTasksTrait {
                equalizeScoreMissing as public;
            }

            public function __construct()
            {
                // Bypass constructor dependencies
            }
        };

        $data = [
            'team1Winners' => [1, null, null],
            'team2Winners' => [null, 0, null],
            'realWinners' => [1, 0, null],
        ];

        [$realWinners, $scores, $updated] = $trait->equalizeScoreMissing($data);

        $this->assertTrue($updated);
        $this->assertEquals([1, 0, null], $realWinners);
        $this->assertEquals([1, 1], $scores);
    }

    /** @test */
    public function it_resolves_scores_and_assigns_next_match_teams()
    {
        $eventId = 1;

        // Seed a "next match"
        $nextMatch = Matches::factory()->create([
            'event_details_id' => $eventId,
            'team1_position' => 99,
            'team1_id' => null,
        ]);

        $deadline = BracketDeadline::factory()->create([
            'event_details_id' => $eventId,
        ]);

        $trait = new class {
            use \App\Console\Commands\DeadlineTasksTrait {
                resolveScores as public;
            }

            public function __construct()
            {
                // Stub constructor, no Firestore used here
            }
        };

        $bracket = [
            'team1_id' => 101,
            'team2_id' => 102,
            'winner_next_position' => 99,
            'loser_next_position' => null,
        ];

        $trait->resolveScores($bracket, [2, 1], $deadline);

        $nextMatch->refresh();

        $this->assertEquals(101, $nextMatch->team1_id);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
