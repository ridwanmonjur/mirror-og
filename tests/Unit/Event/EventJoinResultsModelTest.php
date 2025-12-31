<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use Tests\Traits\{CreatesTestEvents, CreatesTestTeams};
use App\Models\{EventJoinResults, EventDetail, JoinEvent};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventJoinResultsModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestEvents, CreatesTestTeams;

    /** @test */
    public function it_belongs_to_join_event()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $result = EventJoinResults::factory()->create([
            'join_events_id' => $joinEvent->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $result->joinEvent);
    }

    /** @test */
    public function it_stores_position()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $result = EventJoinResults::factory()->create([
            'join_events_id' => $joinEvent->id,
            'position' => 1,
        ]);

        $this->assertEquals(1, $result->position);
    }

    /** @test */
    public function it_stores_prize_sum()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $result = EventJoinResults::factory()->create([
            'join_events_id' => $joinEvent->id,
            'prize_sum' => 1000.00,
        ]);

        $this->assertEquals(1000.00, $result->prize_sum);
    }

    /** @test */
    public function it_stores_match_statistics()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $result = EventJoinResults::factory()->create([
            'join_events_id' => $joinEvent->id,
            'played' => 10,
            'won' => 7,
            'draw' => 2,
            'lost' => 1,
            'points' => 23,
        ]);

        $this->assertEquals(10, $result->played);
        $this->assertEquals(7, $result->won);
        $this->assertEquals(2, $result->draw);
        $this->assertEquals(1, $result->lost);
        $this->assertEquals(23, $result->points);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $result = new EventJoinResults();
        $this->assertFalse($result->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $result = EventJoinResults::factory()->create([
            'join_events_id' => $joinEvent->id,
            'position' => 2,
            'prize_sum' => 500.00,
            'played' => 5,
            'won' => 3,
            'draw' => 1,
            'lost' => 1,
            'points' => 10,
        ]);

        $this->assertEquals($joinEvent->id, $result->join_events_id);
        $this->assertEquals(2, $result->position);
        $this->assertEquals(500.00, $result->prize_sum);
        $this->assertEquals(5, $result->played);
        $this->assertEquals(3, $result->won);
        $this->assertEquals(1, $result->draw);
        $this->assertEquals(1, $result->lost);
        $this->assertEquals(10, $result->points);
    }

    /** @test */
    public function it_tracks_different_positions()
    {
        $event = $this->createEvent();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();
        $team3 = $this->createTeam();

        $joinEvent1 = JoinEvent::factory()->create(['event_details_id' => $event->id, 'team_id' => $team1->id]);
        $joinEvent2 = JoinEvent::factory()->create(['event_details_id' => $event->id, 'team_id' => $team2->id]);
        $joinEvent3 = JoinEvent::factory()->create(['event_details_id' => $event->id, 'team_id' => $team3->id]);

        EventJoinResults::factory()->create(['join_events_id' => $joinEvent1->id, 'position' => 1]);
        EventJoinResults::factory()->create(['join_events_id' => $joinEvent2->id, 'position' => 2]);
        EventJoinResults::factory()->create(['join_events_id' => $joinEvent3->id, 'position' => 3]);

        $results = EventJoinResults::whereIn('join_events_id', [
            $joinEvent1->id,
            $joinEvent2->id,
            $joinEvent3->id,
        ])->orderBy('position')->get();

        $this->assertCount(3, $results);
        $this->assertEquals(1, $results[0]->position);
        $this->assertEquals(2, $results[1]->position);
        $this->assertEquals(3, $results[2]->position);
    }

    /** @test */
    public function it_can_update_results()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $result = EventJoinResults::factory()->create([
            'join_events_id' => $joinEvent->id,
            'played' => 5,
            'won' => 2,
        ]);

        $result->update([
            'played' => 6,
            'won' => 3,
        ]);

        $fresh = $result->fresh();
        $this->assertEquals(6, $fresh->played);
        $this->assertEquals(3, $fresh->won);
    }
}
