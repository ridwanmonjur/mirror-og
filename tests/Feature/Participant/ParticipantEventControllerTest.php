<?php

namespace Tests\Feature\Participant;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams};
use App\Models\{JoinEvent, Like};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantEventControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
    }

    /** @test */
    public function participant_can_view_event_details()
    {
        $event = $this->createEvent();

        $response = $this->actingAs($this->participant)
            ->get("/participant/events/{$event->id}");

        $response->assertStatus(200);
        $response->assertViewIs('Participant.Event.Show');
        $response->assertViewHas('event');
    }

    /** @test */
    public function participant_can_view_registration_management()
    {
        $event = $this->createEvent();

        $response = $this->actingAs($this->participant)
            ->get("/participant/events/{$event->id}/registration");

        $response->assertStatus(200);
        $response->assertViewHas('event');
    }

    /** @test */
    public function participant_can_like_event()
    {
        $event = $this->createEvent();

        $response = $this->actingAs($this->participant)
            ->post('/participant/events/like', [
                'event_details_id' => $event->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->participant->id,
            'event_details_id' => $event->id,
        ]);
    }

    /** @test */
    public function participant_can_unlike_event()
    {
        $event = $this->createEvent();

        Like::factory()->create([
            'user_id' => $this->participant->id,
            'event_details_id' => $event->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/events/like', [
                'event_details_id' => $event->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->participant->id,
            'event_details_id' => $event->id,
        ]);
    }

    /** @test */
    public function participant_can_select_team_to_join_event()
    {
        $event = $this->createEvent();
        $team = $this->createTeam([], $this->participant);

        $response = $this->actingAs($this->participant)
            ->post("/participant/events/{$event->id}/select-team", [
                'team_id' => $team->id,
            ]);

        $response->assertRedirect();
    }

    /** @test */
    public function participant_can_create_team_and_join_event()
    {
        $event = $this->createEvent();

        $teamData = [
            'teamName' => 'New Tournament Team',
            'teamTag' => 'NTT',
            'teamDescription' => 'Created for this event',
        ];

        $response = $this->actingAs($this->participant)
            ->post("/participant/events/{$event->id}/create-team", $teamData);

        $response->assertRedirect();

        $this->assertDatabaseHas('teams', [
            'teamName' => 'New Tournament Team',
        ]);
    }

    /** @test */
    public function participant_can_confirm_event_registration()
    {
        $event = $this->createEvent();
        $team = $this->createTeam([], $this->participant);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
            'join_status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/events/confirm-or-cancel', [
                'join_events_id' => $joinEvent->id,
                'action' => 'confirm',
            ]);

        $response->assertRedirect();
    }

    /** @test */
    public function participant_can_cancel_event_registration()
    {
        $event = $this->createEvent();
        $team = $this->createTeam([], $this->participant);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
            'join_status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/participant/events/confirm-or-cancel', [
                'join_events_id' => $joinEvent->id,
                'action' => 'cancel',
            ]);

        $response->assertRedirect();
    }

    /** @test */
    public function guest_cannot_access_participant_event_pages()
    {
        $event = $this->createEvent();

        $this->get("/participant/events/{$event->id}")
            ->assertRedirect('/login');

        $this->get("/participant/events/{$event->id}/registration")
            ->assertRedirect('/login');
    }

    /** @test */
    public function organizer_cannot_access_participant_event_pages()
    {
        $organizer = $this->createOrganizer();
        $event = $this->createEvent();

        $this->actingAs($organizer)
            ->get("/participant/events/{$event->id}")
            ->assertForbidden();
    }

    /** @test */
    public function participant_cannot_like_same_event_twice()
    {
        $event = $this->createEvent();

        Like::factory()->create([
            'user_id' => $this->participant->id,
            'event_details_id' => $event->id,
        ]);

        // Liking again should unlike
        $this->actingAs($this->participant)
            ->post('/participant/events/like', [
                'event_details_id' => $event->id,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->participant->id,
            'event_details_id' => $event->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_for_team_creation()
    {
        $event = $this->createEvent();

        $response = $this->actingAs($this->participant)
            ->post("/participant/events/{$event->id}/create-team", []);

        $response->assertSessionHasErrors();
    }
}
