<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\{AwardResults, Award, Team, EventDetail, JoinEvent};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AwardResultsModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_team()
    {
        $event = EventDetail::factory()->create();
        $team = Team::factory()->create();
        $award = Award::create([
            'title' => 'Champion',
            'description' => 'Tournament winner',
        ]);

        $joinEvent = JoinEvent::create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'confirmed',
        ]);

        $result = AwardResults::create([
            'join_events_id' => $joinEvent->id,
            'award_id' => $award->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(Team::class, $result->team);
        $this->assertEquals($team->id, $result->team->id);
    }

    /** @test */
    public function it_belongs_to_award()
    {
        $event = EventDetail::factory()->create();
        $team = Team::factory()->create();
        $award = Award::create([
            'title' => 'Champion',
            'description' => 'Tournament winner',
        ]);

        $joinEvent = JoinEvent::create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'confirmed',
        ]);

        $result = AwardResults::create([
            'join_events_id' => $joinEvent->id,
            'award_id' => $award->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(Award::class, $result->award);
        $this->assertEquals($award->id, $result->award->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $event = EventDetail::factory()->create();
        $team = Team::factory()->create();
        $award = Award::create([
            'title' => 'Champion',
            'description' => 'Tournament winner',
        ]);

        $joinEvent = JoinEvent::create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'confirmed',
        ]);

        $result = AwardResults::create([
            'join_events_id' => $joinEvent->id,
            'award_id' => $award->id,
            'team_id' => $team->id,
        ]);

        $this->assertEquals($joinEvent->id, $result->join_events_id);
        $this->assertEquals($award->id, $result->award_id);
        $this->assertEquals($team->id, $result->team_id);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $result = new AwardResults();

        $this->assertEquals('awards_results', $result->getTable());
    }

    /** @test */
    public function it_can_get_team_award_results()
    {
        $event = EventDetail::factory()->create();
        $team = Team::factory()->create();
        $award = Award::create([
            'title' => 'Champion',
            'description' => 'Tournament winner',
        ]);

        $joinEvent = JoinEvent::create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'confirmed',
        ]);

        AwardResults::create([
            'join_events_id' => $joinEvent->id,
            'award_id' => $award->id,
            'team_id' => $team->id,
        ]);

        $results = AwardResults::getTeamAwardResults($event->id);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $results);
    }
}
