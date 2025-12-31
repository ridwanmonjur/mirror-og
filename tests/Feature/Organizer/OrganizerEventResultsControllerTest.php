<?php

namespace Tests\Feature\Organizer;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams};
use App\Models\{EventJoinResults, Award, AwardResults, Achievements, JoinEvent, Brackets};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganizerEventResultsControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams;

    private $organizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organizer = $this->createOrganizer();
    }

    /** @test */
    public function organizer_can_view_event_results_page()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/results");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Results.Index');
        $response->assertViewHas('event');
    }

    /** @test */
    public function organizer_cannot_view_results_for_others_event()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/results");

        $response->assertForbidden();
    }

    /** @test */
    public function organizer_can_store_event_results()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);
        $team = $this->createTeam();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'approved',
        ]);

        $resultsData = [
            'join_events_id' => $joinEvent->id,
            'position' => 1,
        ];

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/results', $resultsData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_join_results', [
            'join_events_id' => $joinEvent->id,
            'position' => 1,
        ]);
    }

    /** @test */
    public function organizer_can_create_award()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $awardData = [
            'event_id' => $event->id,
            'award_name' => 'Most Valuable Player',
            'award_description' => 'Best player of the tournament',
        ];

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/awards', $awardData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('awards', [
            'event_id' => $event->id,
            'award_name' => 'Most Valuable Player',
        ]);
    }

    /** @test */
    public function organizer_can_assign_award_to_team()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);
        $team = $this->createTeam();

        $award = Award::factory()->create([
            'event_id' => $event->id,
        ]);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $awardData = [
            'award_id' => $award->id,
            'join_events_id' => $joinEvent->id,
        ];

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/awards', $awardData);

        $response->assertRedirect();

        $this->assertDatabaseHas('award_results', [
            'award_id' => $award->id,
            'join_events_id' => $joinEvent->id,
        ]);
    }

    /** @test */
    public function organizer_can_create_achievement()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $achievementData = [
            'event_id' => $event->id,
            'achievement_name' => 'Speed Demon',
            'achievement_description' => 'Fastest completion time',
        ];

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/achievements', $achievementData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('achievements', [
            'event_id' => $event->id,
            'achievement_name' => 'Speed Demon',
        ]);
    }

    /** @test */
    public function organizer_can_delete_award()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $award = Award::factory()->create([
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($this->organizer)
            ->delete("/organizer/events/{$event->id}/awards/{$award->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('awards', ['id' => $award->id]);
    }

    /** @test */
    public function organizer_cannot_delete_others_award()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);
        $award = Award::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($this->organizer)
            ->delete("/organizer/events/{$event->id}/awards/{$award->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('awards', ['id' => $award->id]);
    }

    /** @test */
    public function organizer_can_delete_achievement()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $achievement = Achievements::factory()->create([
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($this->organizer)
            ->delete("/organizer/achievements/{$achievement->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    }

    /** @test */
    public function organizer_can_view_brackets_management()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/brackets");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Brackets.Index');
    }

    /** @test */
    public function organizer_can_update_bracket_match_result()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $bracket = Brackets::factory()->create([
            'event_details_id' => $event->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
        ]);

        $matchData = [
            'match_id' => $bracket->id,
            'winner_id' => $team1->id,
            'team1_score' => 2,
            'team2_score' => 1,
        ];

        $response = $this->actingAs($this->organizer)
            ->post("/organizer/events/{$event->id}/brackets/upsert", $matchData);

        $response->assertRedirect();

        $bracket->refresh();
        $this->assertEquals($team1->id, $bracket->winner_id);
    }

    /** @test */
    public function guest_cannot_access_results_management()
    {
        $event = $this->createEvent();

        $this->get("/organizer/events/{$event->id}/results")
            ->assertRedirect('/login');

        $this->post('/organizer/results', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function participant_cannot_access_results_management()
    {
        $participant = $this->createParticipant();
        $event = $this->createEvent();

        $this->actingAs($participant)
            ->get("/organizer/events/{$event->id}/results")
            ->assertForbidden();
    }

    /** @test */
    public function it_validates_position_in_results()
    {
        $response = $this->actingAs($this->organizer)
            ->post('/organizer/results', []);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function results_can_have_multiple_positions()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        $join1 = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team1->id,
        ]);

        $join2 = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team2->id,
        ]);

        EventJoinResults::factory()->create([
            'join_events_id' => $join1->id,
            'position' => 1,
        ]);

        EventJoinResults::factory()->create([
            'join_events_id' => $join2->id,
            'position' => 2,
        ]);

        $this->assertDatabaseCount('event_join_results', 2);
    }
}
