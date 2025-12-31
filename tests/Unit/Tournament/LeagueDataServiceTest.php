<?php

namespace Tests\Unit\Tournament;

use Tests\TestCase;
use App\Services\LeagueDataService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LeagueDataServiceTest extends TestCase
{
    use DatabaseTransactions;

    private LeagueDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LeagueDataService();
    }

    /** @test */
    public function it_generates_default_values_for_organizer()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];

        $result = $this->service->generateDefaultValues(true, $userEnums);

        $this->assertIsArray($result);
        $this->assertNull($result['id']);
        $this->assertNull($result['team1_id']);
        $this->assertEquals('TBD', $result['team1_teamName']);
        $this->assertNull($result['team1_teamBanner']);
        $this->assertNull($result['team1_roster']);
        $this->assertNull($result['team2_id']);
        $this->assertEquals('TBD', $result['team2_teamName']);
        $this->assertNull($result['team2_teamBanner']);
        $this->assertNull($result['team2_roster']);
        $this->assertEquals('organizer', $result['user_level']);
    }

    /** @test */
    public function it_generates_default_values_for_public()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];

        $result = $this->service->generateDefaultValues(false, $userEnums);

        $this->assertEquals('public', $result['user_level']);
        $this->assertEquals('TBD', $result['team1_teamName']);
        $this->assertEquals('TBD', $result['team2_teamName']);
    }

    /** @test */
    public function it_gets_prev_values()
    {
        $result = $this->service->getPrevValues();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(8, $result);
        $this->assertArrayHasKey(16, $result);
        $this->assertArrayHasKey(32, $result);

        // League format doesn't use prev values (no elimination)
        $this->assertEquals([], $result[8]);
        $this->assertEquals([], $result[16]);
        $this->assertEquals([], $result[32]);
    }

    /** @test */
    public function it_produces_league_format_for_8_teams()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, 1);

        $this->assertIsArray($result);

        // 8 teams = 7 rounds (each team plays 7 others)
        // Page 1 shows rounds 1-5
        $this->assertArrayHasKey('R1', $result);
        $this->assertArrayHasKey('R2', $result);
        $this->assertArrayHasKey('R3', $result);
        $this->assertArrayHasKey('R4', $result);
        $this->assertArrayHasKey('R5', $result);
    }

    /** @test */
    public function it_calculates_correct_number_of_rounds()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        // 8 teams = 7 rounds
        $this->service->produceBrackets(8, true, $userEnums, null, 1);
        $pagination = $this->service->getPagination();
        $this->assertEquals(7, $pagination['total_rounds']);

        // 16 teams = 15 rounds
        $this->service->produceBrackets(16, true, $userEnums, null, 1);
        $pagination = $this->service->getPagination();
        $this->assertEquals(15, $pagination['total_rounds']);

        // 32 teams = 31 rounds
        $this->service->produceBrackets(32, true, $userEnums, null, 1);
        $pagination = $this->service->getPagination();
        $this->assertEquals(31, $pagination['total_rounds']);
    }

    /** @test */
    public function it_generates_correct_matches_per_round()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, 1);

        // 8 teams = 4 matches per round (8/2)
        $round1Matches = $result['R1']['R1'];
        $this->assertCount(4, $round1Matches);

        // Check match structure
        $firstMatch = $round1Matches[0];
        $this->assertEquals('P1', $firstMatch['team1_position']);
        $this->assertEquals('P2', $firstMatch['team2_position']);
        $this->assertNull($firstMatch['winner_next_position']);
        $this->assertNull($firstMatch['loser_next_position']);
        $this->assertEquals('TBD', $firstMatch['team1_teamName']);
        $this->assertEquals('org', $firstMatch['user_level']);
    }

    /** @test */
    public function it_handles_pagination_correctly()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        // First page (rounds 1-5)
        $this->service->produceBrackets(8, true, $userEnums, null, 1);
        $pagination = $this->service->getPagination();

        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(2, $pagination['total_pages']); // 7 rounds / 5 per page = 2 pages
        $this->assertEquals(5, $pagination['rounds_per_page']);
        $this->assertTrue($pagination['has_next_page']);
        $this->assertFalse($pagination['has_prev_page']);
        $this->assertEquals(1, $pagination['showing_rounds']['from']);
        $this->assertEquals(5, $pagination['showing_rounds']['to']);
    }

    /** @test */
    public function it_handles_second_page_pagination()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        // Second page (rounds 6-7 for 8 teams)
        $result = $this->service->produceBrackets(8, true, $userEnums, null, 2);
        $pagination = $this->service->getPagination();

        $this->assertEquals(2, $pagination['current_page']);
        $this->assertFalse($pagination['has_next_page']);
        $this->assertTrue($pagination['has_prev_page']);
        $this->assertEquals(6, $pagination['showing_rounds']['from']);
        $this->assertEquals(7, $pagination['showing_rounds']['to']);

        // Should only have rounds 6-7
        $this->assertArrayHasKey('R6', $result);
        $this->assertArrayHasKey('R7', $result);
        $this->assertArrayNotHasKey('R8', $result);
    }

    /** @test */
    public function it_handles_all_pages_parameter()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, 'all');

        // Should return all 7 rounds
        $this->assertCount(7, $result);
        $this->assertArrayHasKey('R1', $result);
        $this->assertArrayHasKey('R7', $result);

        $pagination = $this->service->getPagination();
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(1, $pagination['total_pages']);
        $this->assertEquals(7, $pagination['rounds_per_page']);
    }

    /** @test */
    public function it_defaults_to_page_1_when_null()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, null);

        $pagination = $this->service->getPagination();
        $this->assertEquals(1, $pagination['current_page']);
    }

    /** @test */
    public function it_generates_correct_position_numbers()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, 1);

        // Round 1, Match 1: P1 vs P2
        $this->assertEquals('P1', $result['R1']['R1'][0]['team1_position']);
        $this->assertEquals('P2', $result['R1']['R1'][0]['team2_position']);

        // Round 1, Match 2: P3 vs P4
        $this->assertEquals('P3', $result['R1']['R1'][1]['team1_position']);
        $this->assertEquals('P4', $result['R1']['R1'][1]['team2_position']);

        // Round 2, Match 1: P9 vs P10 (after all Round 1 positions)
        $this->assertEquals('P9', $result['R2']['R2'][0]['team1_position']);
        $this->assertEquals('P10', $result['R2']['R2'][0]['team2_position']);
    }

    /** @test */
    public function it_includes_match_order()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, 1);

        $round1Matches = $result['R1']['R1'];

        // Check order starts at 0
        $this->assertEquals(0, $round1Matches[0]['order']);
        $this->assertEquals(1, $round1Matches[1]['order']);
        $this->assertEquals(2, $round1Matches[2]['order']);
        $this->assertEquals(3, $round1Matches[3]['order']);
    }

    /** @test */
    public function it_includes_deadlines_when_provided()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $deadlines = [
            1 => [1 => '2024-12-31 23:59:59'],
            2 => [2 => '2025-01-07 23:59:59'],
        ];

        $result = $this->service->produceBrackets(8, true, $userEnums, $deadlines, 1);

        $this->assertEquals('2024-12-31 23:59:59', $result['R1']['R1'][0]['deadline']);
        $this->assertEquals('2025-01-07 23:59:59', $result['R2']['R2'][0]['deadline']);
    }

    /** @test */
    public function it_handles_null_deadlines()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, 1);

        $this->assertNull($result['R1']['R1'][0]['deadline']);
    }

    /** @test */
    public function it_produces_brackets_without_user_enums()
    {
        $result = $this->service->produceBrackets(8, true, null, null, 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('R1', $result);

        // When USER_ENUMS is null, default values should be empty
        $firstMatch = $result['R1']['R1'][0];
        $this->assertArrayNotHasKey('user_level', $firstMatch);
        $this->assertArrayNotHasKey('team1_teamName', $firstMatch);
    }

    /** @test */
    public function it_gets_round_names_after_producing_brackets()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        // After producing brackets
        $this->service->produceBrackets(8, true, $userEnums, null, 1);
        $roundNames = $this->service->getRoundNames();

        $this->assertIsArray($roundNames);
        $this->assertEquals(['R1', 'R2', 'R3', 'R4', 'R5'], $roundNames);
    }

    /** @test */
    public function it_handles_large_team_numbers()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(32, true, $userEnums, null, 1);

        $this->assertIsArray($result);

        // 32 teams = 16 matches per round
        $round1Matches = $result['R1']['R1'];
        $this->assertCount(16, $round1Matches);

        $pagination = $this->service->getPagination();
        $this->assertEquals(31, $pagination['total_rounds']);
        $this->assertEquals(7, $pagination['total_pages']); // ceil(31 / 5)
    }

    /** @test */
    public function it_maintains_no_progression_for_league_format()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        $result = $this->service->produceBrackets(8, true, $userEnums, null, 1);

        // In league format, there's no winner/loser progression
        foreach ($result as $round) {
            foreach ($round as $matches) {
                foreach ($matches as $match) {
                    $this->assertNull($match['winner_next_position']);
                    $this->assertNull($match['loser_next_position']);
                }
            }
        }
    }
}
