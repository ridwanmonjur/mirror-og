<?php

namespace Tests\Unit\Tournament;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams};
use App\Models\{Brackets, EventDetail, Team, BracketDeadline};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BracketsModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams;

    /** @test */
    public function it_belongs_to_team1()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
        ]);

        $this->assertInstanceOf(Team::class, $bracket->team1);
        $this->assertEquals($team1->id, $bracket->team1->id);
    }

    /** @test */
    public function it_belongs_to_team2()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
        ]);

        $this->assertInstanceOf(Team::class, $bracket->team2);
        $this->assertEquals($team2->id, $bracket->team2->id);
    }

    /** @test */
    public function it_belongs_to_event()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $bracket->event);
        $this->assertEquals($event->id, $bracket->event->id);
    }

    /** @test */
    public function it_stores_team_scores()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'team1_score' => 10,
            'team2_score' => 8,
        ]);

        $this->assertEquals(10, $bracket->team1_score);
        $this->assertEquals(8, $bracket->team2_score);
    }

    /** @test */
    public function it_stores_team_points()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'team1_points' => 100,
            'team2_points' => 75,
        ]);

        $this->assertEquals(100, $bracket->team1_points);
        $this->assertEquals(75, $bracket->team2_points);
    }

    /** @test */
    public function it_stores_bracket_positions()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'team1_position' => 1,
            'team2_position' => 2,
        ]);

        $this->assertEquals(1, $bracket->team1_position);
        $this->assertEquals(2, $bracket->team2_position);
    }

    /** @test */
    public function it_stores_loser_next_position()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'loser_next_position' => 5,
        ]);

        $this->assertEquals(5, $bracket->loser_next_position);
    }

    /** @test */
    public function it_stores_stage_names()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'stage_name' => 'Finals',
            'inner_stage_name' => 'Grand Final',
        ]);

        $this->assertEquals('Finals', $bracket->stage_name);
        $this->assertEquals('Grand Final', $bracket->inner_stage_name);
    }

    /** @test */
    public function it_stores_bracket_status()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'completed',
        ]);

        $this->assertEquals('completed', $bracket->status);
    }

    /** @test */
    public function it_stores_bracket_result()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'result' => 'team1_win',
        ]);

        $this->assertEquals('team1_win', $bracket->result);
    }

    /** @test */
    public function it_filters_by_single_deadline()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket1 = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'stage_name' => 'Finals',
            'inner_stage_name' => 'Grand Final',
        ]);

        $bracket2 = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'stage_name' => 'Semi Finals',
            'inner_stage_name' => 'Match 1',
        ]);

        $deadline = (object) ['stage' => 'Finals', 'inner_stage' => 'Grand Final'];

        $results = Brackets::filterByDeadlines([$deadline])->get();

        $this->assertCount(1, $results);
        $this->assertEquals($bracket1->id, $results->first()->id);
    }

    /** @test */
    public function it_filters_by_multiple_deadlines()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket1 = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'stage_name' => 'Finals',
            'inner_stage_name' => 'Grand Final',
        ]);

        $bracket2 = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'stage_name' => 'Semi Finals',
            'inner_stage_name' => 'Match 1',
        ]);

        $bracket3 = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'stage_name' => 'Quarter Finals',
            'inner_stage_name' => 'Match 1',
        ]);

        $deadlines = [
            (object) ['stage' => 'Finals', 'inner_stage' => 'Grand Final'],
            (object) ['stage' => 'Semi Finals', 'inner_stage' => 'Match 1'],
        ];

        $results = Brackets::filterByDeadlines($deadlines)->get();

        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_stores_bracket_order()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'order' => 3,
        ]);

        $this->assertEquals(3, $bracket->order);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'order' => 1,
            'team1_score' => 10,
            'team2_score' => 5,
            'stage_name' => 'Finals',
            'status' => 'completed',
        ]);

        $this->assertEquals(1, $bracket->order);
        $this->assertEquals(10, $bracket->team1_score);
        $this->assertEquals(5, $bracket->team2_score);
        $this->assertEquals('Finals', $bracket->stage_name);
        $this->assertEquals('completed', $bracket->status);
    }
}
