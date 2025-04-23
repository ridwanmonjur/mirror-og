<?php

namespace Tests\Unit\Commands;

use App\Models\BracketDeadline;
use App\Models\Matches;
use App\Console\Commands\DeadlineTasksTrait;
use Mockery;
use Tests\TestCase;

class PureDeadlineTasksTraitTest extends TestCase
{
    protected $traitObject;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitObject = new class {
            use DeadlineTasksTrait {
                __construct as private traitConstruct;
            }
            
            // Override constructor to avoid database connections
            public function __construct() {}
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function equalizeScoreMissing_correctly_handles_missing_team1_scores()
    {
        // Arrange
        $matchStatusData = [
            'team1Winners' => [null, null, null],
            'team2Winners' => [1, 1, 1], 
            'realWinners' => [null, null, null],
        ];

        // Act
        [$realWinners, $scores, $updated] = $this->traitObject->equalizeScoreMissing($matchStatusData);

        // Assert
        $this->assertEquals([1, 1, 1], $realWinners);
        $this->assertEquals([0, 3], $scores);
        $this->assertTrue($updated);
    }

    /** @test */
    public function equalizeScoreMissing_correctly_handles_missing_team2_scores()
    {
        // Arrange
        $matchStatusData = [
            'team1Winners' => [0, 0, 0],
            'team2Winners' => [null, null, null],
            'realWinners' => [null, null, null],
        ];

        // Act
        [$realWinners, $scores, $updated] = $this->traitObject->equalizeScoreMissing($matchStatusData);

        // Assert
        $this->assertEquals([0, 0, 0], $realWinners);
        $this->assertEquals([3, 0], $scores);
        $this->assertTrue($updated);
    }

    /** @test */
    public function equalizeScoreMissing_correctly_handles_mixed_missing_scores()
    {
        // Arrange
        $matchStatusData = [
            'team1Winners' => [0, null, 0],
            'team2Winners' => [null, 1, null],
            'realWinners' => [null, null, null],
        ];

        // Act
        [$realWinners, $scores, $updated] = $this->traitObject->equalizeScoreMissing($matchStatusData);

        // Assert
        $this->assertEquals([0, 1, 0], $realWinners);
        $this->assertEquals([2, 1], $scores);
        $this->assertTrue($updated);
    }

    /** @test */
    public function equalizeScoreMissing_returns_false_when_no_updates_needed()
    {
        // Arrange
        $matchStatusData = [
            'team1Winners' => [0, 1, 0],
            'team2Winners' => [0, 1, 0],
            'realWinners' => [0, 1, 0],
        ];

        // Act
        [$realWinners, $scores, $updated] = $this->traitObject->equalizeScoreMissing($matchStatusData);

        // Assert
        $this->assertEquals([0, 1, 0], $realWinners);
        $this->assertFalse($updated);
    }

    /** @test */
    public function resolveScores_handles_team1_win_correctly()
    {
        // Arrange
        $bracket = [
            'team1_id' => 101,
            'team2_id' => 102,
            'winner_next_position' => 'W3',
            'loser_next_position' => 'L3',
            'team1_position' => 'W1',
            'team2_position' => 'W2'
        ];
        
        $scores = [2, 1]; // Team 1 wins
        
        $deadline = Mockery::mock(BracketDeadline::class);
        $deadline->shouldReceive('getAttribute')->with('event_details_id')->andReturn(47);
        
        // Mock the Matches query
        $winnerMatchQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $winnerMatchQuery->shouldReceive('where')->andReturnSelf();
        $winnerMatchQuery->shouldReceive('orWhere')->andReturnSelf();
        
        $loserMatchQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $loserMatchQuery->shouldReceive('where')->andReturnSelf();
        $loserMatchQuery->shouldReceive('orWhere')->andReturnSelf();
        
        // Mock winner match records
        $winnerMatch = Mockery::mock(Matches::class);
        $winnerMatch->shouldReceive('getAttribute')->with('team1_position')->andReturn('W3');
        $winnerMatch->shouldReceive('getAttribute')->with('team2_position')->andReturn('X1');
        $winnerMatch->shouldReceive('getAttribute')->with('team1_id')->andReturn(null);
        $winnerMatch->shouldReceive('setAttribute')->with('team1_id', 101);
        $winnerMatch->shouldReceive('save')->once();
        
        // Mock loser match records
        $loserMatch = Mockery::mock(Matches::class);
        $loserMatch->shouldReceive('getAttribute')->with('team1_position')->andReturn('X2');
        $loserMatch->shouldReceive('getAttribute')->with('team2_position')->andReturn('L3');
        $loserMatch->shouldReceive('getAttribute')->with('team2_id')->andReturn(null);
        $loserMatch->shouldReceive('setAttribute')->with('team2_id', 102);
        $loserMatch->shouldReceive('save')->once();
        
        $winnerMatchQuery->shouldReceive('get')->andReturn(collect([$winnerMatch]));
        $loserMatchQuery->shouldReceive('get')->andReturn(collect([$loserMatch]));
        
        // Mock the Matches::where static method
        Matches::shouldReceive('where')
            ->with('event_details_id', 47)
            ->andReturn($winnerMatchQuery)
            ->once();
            
        Matches::shouldReceive('where')
            ->with('event_details_id', 47)
            ->andReturn($loserMatchQuery)
            ->once();
        
        // Act
        $this->traitObject->resolveScores($bracket, $scores, $deadline);
        
        // Assert - Mockery will verify all expectations
    }

    /** @test */
    public function resolveScores_handles_team2_win_correctly()
    {
        // Arrange
        $bracket = [
            'team1_id' => 101,
            'team2_id' => 102,
            'winner_next_position' => 'W3',
            'loser_next_position' => 'L3',
            'team1_position' => 'W1',
            'team2_position' => 'W2'
        ];
        
        $scores = [1, 2]; // Team 2 wins
        
        $deadline = Mockery::mock(BracketDeadline::class);
        $deadline->shouldReceive('getAttribute')->with('event_details_id')->andReturn(47);
        
        // Mock the Matches query
        $winnerMatchQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $winnerMatchQuery->shouldReceive('where')->andReturnSelf();
        $winnerMatchQuery->shouldReceive('orWhere')->andReturnSelf();
        
        $loserMatchQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $loserMatchQuery->shouldReceive('where')->andReturnSelf();
        $loserMatchQuery->shouldReceive('orWhere')->andReturnSelf();
        
        // Mock winner match records
        $winnerMatch = Mockery::mock(Matches::class);
        $winnerMatch->shouldReceive('getAttribute')->with('team1_position')->andReturn('W3');
        $winnerMatch->shouldReceive('getAttribute')->with('team2_position')->andReturn('X1');
        $winnerMatch->shouldReceive('getAttribute')->with('team1_id')->andReturn(null);
        $winnerMatch->shouldReceive('setAttribute')->with('team1_id', 102);
        $winnerMatch->shouldReceive('save')->once();
        
        // Mock loser match records
        $loserMatch = Mockery::mock(Matches::class);
        $loserMatch->shouldReceive('getAttribute')->with('team1_position')->andReturn('X2');
        $loserMatch->shouldReceive('getAttribute')->with('team2_position')->andReturn('L3');
        $loserMatch->shouldReceive('getAttribute')->with('team2_id')->andReturn(null);
        $loserMatch->shouldReceive('setAttribute')->with('team2_id', 101);
        $loserMatch->shouldReceive('save')->once();
        
        $winnerMatchQuery->shouldReceive('get')->andReturn(collect([$winnerMatch]));
        $loserMatchQuery->shouldReceive('get')->andReturn(collect([$loserMatch]));
        
        // Mock the Matches::where static method
        Matches::shouldReceive('where')
            ->with('event_details_id', 47)
            ->andReturn($winnerMatchQuery)
            ->once();
            
        Matches::shouldReceive('where')
            ->with('event_details_id', 47)
            ->andReturn($loserMatchQuery)
            ->once();
        
        // Act
        $this->traitObject->resolveScores($bracket, $scores, $deadline);
        
        // Assert - Mockery will verify all expectations
    }

    /** @test */
    public function resolveScores_handles_ties_correctly()
    {
        // Mock the random function to return a predictable result
        $randomReturn = 0; // Choose team 1 as winner
        Mockery::mock('alias:rand')->shouldReceive('__invoke')->with(0, 1)->andReturn($randomReturn);
        
        // Arrange
        $bracket = [
            'team1_id' => 101,
            'team2_id' => 102,
            'winner_next_position' => 'W3',
            'loser_next_position' => 'L3',
            'team1_position' => 'W1',
            'team2_position' => 'W2'
        ];
        
        $scores = [1, 1]; // Tie
        
        $deadline = Mockery::mock(BracketDeadline::class);
        $deadline->shouldReceive('getAttribute')->with('event_details_id')->andReturn(47);
        
        // Mock the Matches query
        $winnerMatchQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $winnerMatchQuery->shouldReceive('where')->andReturnSelf();
        $winnerMatchQuery->shouldReceive('orWhere')->andReturnSelf();
        
        $loserMatchQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $loserMatchQuery->shouldReceive('where')->andReturnSelf();
        $loserMatchQuery->shouldReceive('orWhere')->andReturnSelf();
        
        // Mock winner match records
        $winnerMatch = Mockery::mock(Matches::class);
        $winnerMatch->shouldReceive('getAttribute')->with('team1_position')->andReturn('W3');
        $winnerMatch->shouldReceive('getAttribute')->with('team2_position')->andReturn('X1');
        $winnerMatch->shouldReceive('getAttribute')->with('team1_id')->andReturn(null);
        $winnerMatch->shouldReceive('setAttribute')->with('team1_id', 101);
        $winnerMatch->shouldReceive('save')->once();
        
        // Mock loser match records
        $loserMatch = Mockery::mock(Matches::class);
        $loserMatch->shouldReceive('getAttribute')->with('team1_position')->andReturn('X2');
        $loserMatch->shouldReceive('getAttribute')->with('team2_position')->andReturn('L3');
        $loserMatch->shouldReceive('getAttribute')->with('team2_id')->andReturn(null);
        $loserMatch->shouldReceive('setAttribute')->with('team2_id', 102);
        $loserMatch->shouldReceive('save')->once();
        
        $winnerMatchQuery->shouldReceive('get')->andReturn(collect([$winnerMatch]));
        $loserMatchQuery->shouldReceive('get')->andReturn(collect([$loserMatch]));
        
        // Mock the Matches::where static method
        Matches::shouldReceive('where')
            ->with('event_details_id', 47)
            ->andReturn($winnerMatchQuery)
            ->once();
            
        Matches::shouldReceive('where')
            ->with('event_details_id', 47)
            ->andReturn($loserMatchQuery)
            ->once();
        
        // Act
        $this->traitObject->resolveScores($bracket, $scores, $deadline);
        
        // Assert - Mockery will verify all expectations
    }

    /** @test */
    public function resolveScores_handles_missing_loser_next_position()
    {
        // Arrange
        $bracket = [
            'team1_id' => 101,
            'team2_id' => 102,
            'winner_next_position' => 'W3',
            'loser_next_position' => null, // No loser bracket
            'team1_position' => 'W1',
            'team2_position' => 'W2'
        ];
        
        $scores = [2, 1]; // Team 1 wins
        
        $deadline = Mockery::mock(BracketDeadline::class);
        $deadline->shouldReceive('getAttribute')->with('event_details_id')->andReturn(47);
        
        // Mock the Matches query
        $winnerMatchQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $winnerMatchQuery->shouldReceive('where')->andReturnSelf();
        $winnerMatchQuery->shouldReceive('orWhere')->andReturnSelf();
        
        // Mock winner match records
        $winnerMatch = Mockery::mock(Matches::class);
        $winnerMatch->shouldReceive('getAttribute')->with('team1_position')->andReturn('W3');
        $winnerMatch->shouldReceive('getAttribute')->with('team2_position')->andReturn('X1');
        $winnerMatch->shouldReceive('getAttribute')->with('team1_id')->andReturn(null);
        $winnerMatch->shouldReceive('setAttribute')->with('team1_id', 101);
        $winnerMatch->shouldReceive('save')->once();
        
        $winnerMatchQuery->shouldReceive('get')->andReturn(collect([$winnerMatch]));
        
        // Mock the Matches::where static method
        Matches::shouldReceive('where')
            ->with('event_details_id', 47)
            ->andReturn($winnerMatchQuery)
            ->once();
        
        // Act
        $this->traitObject->resolveScores($bracket, $scores, $deadline);
        
        // Assert - Mockery will verify all expectations
    }
}