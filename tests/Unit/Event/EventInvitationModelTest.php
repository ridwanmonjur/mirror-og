<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams};
use App\Models\{EventInvitation, User, Team, EventDetail};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventInvitationModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams;

    /** @test */
    public function it_belongs_to_organizer()
    {
        $organizer = $this->createOrganizer();
        $participant = $this->createParticipant();
        $event = $this->createEvent([], $organizer);
        $team = $this->createTeam();

        $invitation = EventInvitation::factory()->create([
            'organizer_user_id' => $organizer->id,
            'participant_user_id' => $participant->id,
            'team_id' => $team->id,
            'event_id' => $event->id,
        ]);

        $this->assertInstanceOf(User::class, $invitation->organizer);
        $this->assertEquals($organizer->id, $invitation->organizer->id);
    }

    /** @test */
    public function it_belongs_to_participant()
    {
        $organizer = $this->createOrganizer();
        $participant = $this->createParticipant();
        $event = $this->createEvent([], $organizer);
        $team = $this->createTeam();

        $invitation = EventInvitation::factory()->create([
            'organizer_user_id' => $organizer->id,
            'participant_user_id' => $participant->id,
            'team_id' => $team->id,
            'event_id' => $event->id,
        ]);

        $this->assertInstanceOf(User::class, $invitation->participant);
        $this->assertEquals($participant->id, $invitation->participant->id);
    }

    /** @test */
    public function it_belongs_to_team()
    {
        $organizer = $this->createOrganizer();
        $participant = $this->createParticipant();
        $event = $this->createEvent([], $organizer);
        $team = $this->createTeam();

        $invitation = EventInvitation::factory()->create([
            'organizer_user_id' => $organizer->id,
            'participant_user_id' => $participant->id,
            'team_id' => $team->id,
            'event_id' => $event->id,
        ]);

        $this->assertInstanceOf(Team::class, $invitation->team);
        $this->assertEquals($team->id, $invitation->team->id);
    }

    /** @test */
    public function it_belongs_to_event()
    {
        $organizer = $this->createOrganizer();
        $participant = $this->createParticipant();
        $event = $this->createEvent([], $organizer);
        $team = $this->createTeam();

        $invitation = EventInvitation::factory()->create([
            'organizer_user_id' => $organizer->id,
            'participant_user_id' => $participant->id,
            'team_id' => $team->id,
            'event_id' => $event->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $invitation->event);
        $this->assertEquals($event->id, $invitation->event->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $organizer = $this->createOrganizer();
        $participant = $this->createParticipant();
        $event = $this->createEvent([], $organizer);
        $team = $this->createTeam();

        $invitation = EventInvitation::factory()->create([
            'organizer_user_id' => $organizer->id,
            'participant_user_id' => $participant->id,
            'team_id' => $team->id,
            'event_id' => $event->id,
        ]);

        $this->assertEquals($organizer->id, $invitation->organizer_user_id);
        $this->assertEquals($participant->id, $invitation->participant_user_id);
        $this->assertEquals($team->id, $invitation->team_id);
        $this->assertEquals($event->id, $invitation->event_id);
    }
}
