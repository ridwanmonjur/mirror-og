<?php
namespace Tests\Unit;

use App\Models\Brackets;
use App\Models\BracketDeadline;
use Database\Factories\TeamFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

// class DeadlineTasksTest extends TestCase
// {

//     protected function setUp(): void
//     {
//         parent::setUp();
        
//         UserFactory::deleteRelatedTables();
//         TeamFactory::deleteRelatedTables();

//     }

//     /** @test */
//     public function it_equalizes_missing_scores_correctly()
//     {
//         $trait = new class {
//             use \App\Console\Commands\DeadlineTasksTrait {
//                 equalizeScoreMissing as public;
//             }

//             public function __construct()
//             {
//                 // Bypass constructor dependencies
//             }
//         };

//         $data = [
//             'team1Winners' => [1, null, null],
//             'team2Winners' => [null, 0, null],
//             'realWinners' => [1, 0, null],
//         ];

//         [$realWinners, $scores, $updated] = $trait->equalizeScoreMissing($data);

//         $this->assertTrue($updated);
//         $this->assertEquals([1, 0, null], $realWinners);
//         $this->assertEquals([1, 1], $scores);
//     }

//     /** @test */
//     public function it_resolves_scores_and_assigns_next_match_teams()
//     {

//         $user = \App\Models\User::factory()->create();
//         $team1 = \App\Models\Team::factory()->create();
//         $team2 = \App\Models\Team::factory()->create();
    
//         // Create event details connected to that user
//         $eventDetail = \App\Models\EventDetail::factory()->create([
//             'user_id' => $user->id
//         ]);

//         // Seed a "next match"
//         $nextMatch = Brackets::factory()->create([
//             'event_details_id' => $eventDetail->id,
//             'team1_position' => '99',
//             'team1_id' => null,
//         ]);

//         $deadline = BracketDeadline::factory()->create([
//             'event_details_id' => $eventDetail->id,
//         ]);

//         $trait = new class {
//             use \App\Console\Commands\DeadlineTasksTrait {
//                 resolveScores as public;
//             }

//             public function __construct()
//             {
//                 // Stub constructor, no Firestore used here
//             }
//         };

//         $bracket = [
//             'team1_id' => $team1->id,
//             'team2_id' => $team2->id,
//             'winner_next_position' => 99,
//             'loser_next_position' => null,
//         ];

//         $trait->resolveScores($bracket, [2, 1], $deadline);

//         $nextMatch->refresh();

//         $this->assertEquals($team1->id, $nextMatch->team1_id);
//     }

//     protected function tearDown(): void
//     {
//         parent::tearDown();
//         Mockery::close();
//     }
// }
