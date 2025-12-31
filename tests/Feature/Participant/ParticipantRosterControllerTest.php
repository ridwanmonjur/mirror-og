<?php

namespace Tests\Feature\Participant;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams};
use App\Models\{JoinEvent, RosterMember, RosterCaptain};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantRosterControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
    }

    /** @test */
    public function participant_can_approve_roster_member()
    {
        $team = $this->createTeam([], $this->participant);
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
        ]);

        $member = $this->createParticipant();

        $rosterMember = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $member->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/roster/approve', [
                'roster_member_id' => $rosterMember->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $rosterMember->refresh();
        $this->assertEquals('approved', $rosterMember->status);
    }

    /** @test */
    public function participant_can_disapprove_roster_member()
    {
        $team = $this->createTeam([], $this->participant);
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
        ]);

        $member = $this->createParticipant();

        $rosterMember = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $member->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/roster/disapprove', [
                'roster_member_id' => $rosterMember->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('roster_members', [
            'id' => $rosterMember->id,
        ]);
    }

    /** @test */
    public function participant_can_vote_to_stay_in_event()
    {
        $team = $this->createTeam([], $this->participant);
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
            'vote_ongoing' => true,
        ]);

        $rosterMember = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $this->participant->id,
            'vote_to_quit' => null,
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/roster/vote', [
                'join_events_id' => $joinEvent->id,
                'vote' => 'stay',
            ]);

        $response->assertRedirect();

        $rosterMember->refresh();
        $this->assertEquals(0, $rosterMember->vote_to_quit);
    }

    /** @test */
    public function participant_can_vote_to_leave_event()
    {
        $team = $this->createTeam([], $this->participant);
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
            'vote_ongoing' => true,
        ]);

        $rosterMember = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $this->participant->id,
            'vote_to_quit' => null,
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/roster/vote', [
                'join_events_id' => $joinEvent->id,
                'vote' => 'leave',
            ]);

        $response->assertRedirect();

        $rosterMember->refresh();
        $this->assertEquals(1, $rosterMember->vote_to_quit);
    }

    /** @test */
    public function participant_can_set_roster_captain()
    {
        $team = $this->createTeam([], $this->participant);
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
        ]);

        $member = $this->createParticipant();

        $rosterMember = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $member->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/roster/captain', [
                'roster_member_id' => $rosterMember->id,
                'join_events_id' => $joinEvent->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('rosters_captain', [
            'join_events_id' => $joinEvent->id,
            'team_member_id' => $rosterMember->id,
        ]);
    }

    /** @test */
    public function participant_cannot_approve_roster_for_others_team()
    {
        $otherParticipant = $this->createParticipant();
        $team = $this->createTeam([], $otherParticipant);
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $otherParticipant->id,
        ]);

        $rosterMember = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $this->participant->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/roster/approve', [
                'roster_member_id' => $rosterMember->id,
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function guest_cannot_access_roster_management()
    {
        $this->post('/participant/roster/approve', [])
            ->assertRedirect('/login');

        $this->post('/participant/roster/vote', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function organizer_cannot_access_participant_roster_pages()
    {
        $organizer = $this->createOrganizer();

        $this->actingAs($organizer)
            ->post('/participant/roster/approve', [])
            ->assertForbidden();
    }

    /** @test */
    public function it_validates_required_fields_for_roster_approval()
    {
        $response = $this->actingAs($this->participant)
            ->post('/participant/roster/approve', []);

        $response->assertSessionHasErrors();
    }
}
