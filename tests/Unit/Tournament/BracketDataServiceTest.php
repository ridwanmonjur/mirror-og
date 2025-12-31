<?php

namespace Tests\Unit\Tournament;

use Tests\TestCase;
use App\Services\BracketDataService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BracketDataServiceTest extends TestCase
{
    use DatabaseTransactions;

    private BracketDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BracketDataService();
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

        $this->assertIsArray($result);
        $this->assertEquals('public', $result['user_level']);
        $this->assertEquals('TBD', $result['team1_teamName']);
        $this->assertEquals('TBD', $result['team2_teamName']);
    }

    /** @test */
    public function it_gets_prev_values()
    {
        $result = $this->service->getPrevValues();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(32, $result);
        $this->assertArrayHasKey(16, $result);
        $this->assertArrayHasKey(8, $result);
    }

    /** @test */
    public function it_has_prev_values_for_32_teams()
    {
        $result = $this->service->getPrevValues();

        $this->assertArrayHasKey('F', $result[32]);
        $this->assertArrayHasKey('U1', $result[32]);
        $this->assertArrayHasKey('L1', $result[32]);

        // Check Finals structure
        $this->assertEquals(['G1', 'G2'], $result[32]['F']);

        // Check first upper bracket match
        $this->assertEquals(['W1', 'W2'], $result[32]['U1']);

        // Check first lower bracket match
        $this->assertEquals(['W1', 'W2'], $result[32]['L1']);
    }

    /** @test */
    public function it_has_prev_values_for_16_teams()
    {
        $result = $this->service->getPrevValues();

        $this->assertArrayHasKey('F', $result[16]);
        $this->assertArrayHasKey('U1', $result[16]);
        $this->assertArrayHasKey('L1', $result[16]);

        $this->assertEquals(['G1', 'G2'], $result[16]['F']);
        $this->assertEquals(['W1', 'W2'], $result[16]['U1']);
    }

    /** @test */
    public function it_has_prev_values_for_8_teams()
    {
        $result = $this->service->getPrevValues();

        $this->assertArrayHasKey('F', $result[8]);
        $this->assertArrayHasKey('U1', $result[8]);
        $this->assertArrayHasKey('L1', $result[8]);

        $this->assertEquals(['G1', 'G2'], $result[8]['F']);
        $this->assertEquals(['W1', 'W2'], $result[8]['U1']);
    }

    /** @test */
    public function it_gets_pagination()
    {
        $result = $this->service->getPagination();

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['current_page']);
        $this->assertEquals(1, $result['total_pages']);
        $this->assertEquals(3, $result['total_rounds']);
        $this->assertEquals(3, $result['rounds_per_page']);
        $this->assertFalse($result['has_next_page']);
        $this->assertFalse($result['has_prev_page']);
        $this->assertArrayHasKey('showing_rounds', $result);
        $this->assertEquals(1, $result['showing_rounds']['from']);
        $this->assertEquals(3, $result['showing_rounds']['to']);
    }

    /** @test */
    public function it_gets_round_names()
    {
        $result = $this->service->getRoundNames();

        $this->assertIsArray($result);
        $this->assertEquals(['U', 'L', 'F'], $result);
    }

    /** @test */
    public function it_produces_brackets_for_32_teams()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];
        $deadlines = [];
        $page = 1;

        $result = $this->service->produceBrackets(32, true, $userEnums, $deadlines, $page);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('F', $result);
        $this->assertArrayHasKey('U', $result);
        $this->assertArrayHasKey('L', $result);

        // Check Finals structure
        $this->assertArrayHasKey('F', $result['F']);
        $this->assertArrayHasKey('W', $result['F']);

        // Check Upper bracket has rounds
        $this->assertArrayHasKey('e1', $result['U']);
        $this->assertArrayHasKey('e2', $result['U']);

        // Check Lower bracket has rounds
        $this->assertArrayHasKey('e1', $result['L']);
    }

    /** @test */
    public function it_produces_correct_structure_for_32_teams_finals()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];

        $result = $this->service->produceBrackets(32, true, $userEnums, [], 1);

        // Check Grand Final match
        $grandFinal = $result['F']['F'][0];
        $this->assertEquals('G1', $grandFinal['team1_position']);
        $this->assertEquals('G2', $grandFinal['team2_position']);
        $this->assertEquals('F', $grandFinal['winner_next_position']);
        $this->assertNull($grandFinal['loser_next_position']);
        $this->assertEquals('TBD', $grandFinal['team1_teamName']);
        $this->assertEquals('TBD', $grandFinal['team2_teamName']);
        $this->assertEquals('organizer', $grandFinal['user_level']);
    }

    /** @test */
    public function it_produces_brackets_for_16_teams()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];

        $result = $this->service->produceBrackets(16, false, $userEnums, [], 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('F', $result);
        $this->assertArrayHasKey('U', $result);
        $this->assertArrayHasKey('L', $result);

        // Verify user level for public
        $grandFinal = $result['F']['F'][0];
        $this->assertEquals('public', $grandFinal['user_level']);
    }

    /** @test */
    public function it_produces_brackets_for_8_teams()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];

        $result = $this->service->produceBrackets(8, true, $userEnums, [], 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('F', $result);
        $this->assertArrayHasKey('U', $result);
        $this->assertArrayHasKey('L', $result);
    }

    /** @test */
    public function it_includes_deadlines_in_bracket_structure()
    {
        $userEnums = [
            'IS_ORGANIZER' => 'organizer',
            'IS_PUBLIC' => 'public',
        ];
        $deadlines = [
            'F' => [
                'F' => '2024-12-31 23:59:59',
                'W' => '2025-01-07 23:59:59',
            ],
            'U' => [
                'e1' => '2024-12-15 23:59:59',
            ],
        ];

        $result = $this->service->produceBrackets(32, true, $userEnums, $deadlines, 1);

        // Check Finals deadline
        $this->assertEquals('2024-12-31 23:59:59', $result['F']['F'][0]['deadline']);
        $this->assertEquals('2025-01-07 23:59:59', $result['F']['W'][0]['deadline']);

        // Check Upper bracket deadline
        $this->assertEquals('2024-12-15 23:59:59', $result['U']['e1'][0]['deadline']);
    }

    /** @test */
    public function it_produces_brackets_without_user_enums()
    {
        $result = $this->service->produceBrackets(32, true, null, [], 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('F', $result);
        $this->assertArrayHasKey('U', $result);
        $this->assertArrayHasKey('L', $result);

        // When USER_ENUMS is null, default values should be empty array
        // So these keys should not exist
        $grandFinal = $result['F']['F'][0];
        $this->assertArrayNotHasKey('user_level', $grandFinal);
        $this->assertArrayNotHasKey('team1_teamName', $grandFinal);
    }

    /** @test */
    public function it_produces_correct_upper_bracket_progression_for_32_teams()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];
        $result = $this->service->produceBrackets(32, true, $userEnums, [], 1);

        // Check first round matches progress to second round
        $firstMatch = $result['U']['e1'][0];
        $this->assertEquals('W1', $firstMatch['team1_position']);
        $this->assertEquals('W2', $firstMatch['team2_position']);
        $this->assertEquals('U1', $firstMatch['winner_next_position']);
        $this->assertEquals('L1', $firstMatch['loser_next_position']);
    }

    /** @test */
    public function it_produces_correct_lower_bracket_structure()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];
        $result = $this->service->produceBrackets(32, true, $userEnums, [], 1);

        // Lower bracket should have multiple elimination rounds
        $this->assertArrayHasKey('e1', $result['L']);
        $this->assertIsArray($result['L']['e1']);
        $this->assertNotEmpty($result['L']['e1']);
    }

    /** @test */
    public function it_maintains_consistent_bracket_structure_across_team_sizes()
    {
        $userEnums = ['IS_ORGANIZER' => 'org', 'IS_PUBLIC' => 'pub'];

        foreach ([8, 16, 32] as $teamCount) {
            $result = $this->service->produceBrackets($teamCount, true, $userEnums, [], 1);

            // All should have same top-level structure
            $this->assertArrayHasKey('F', $result, "Team count $teamCount missing Finals");
            $this->assertArrayHasKey('U', $result, "Team count $teamCount missing Upper bracket");
            $this->assertArrayHasKey('L', $result, "Team count $teamCount missing Lower bracket");

            // All should have Grand Final and Winner's Final
            $this->assertArrayHasKey('F', $result['F'], "Team count $teamCount missing Grand Final");
            $this->assertArrayHasKey('W', $result['F'], "Team count $teamCount missing Winner's bracket");
        }
    }
}
