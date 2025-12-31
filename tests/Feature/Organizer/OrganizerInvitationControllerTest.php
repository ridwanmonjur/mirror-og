<?php

namespace Tests\Feature\Organizer;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents};
use App\Models\EventInvitation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventInvitationMail;

class OrganizerInvitationControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents;

    private $organizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organizer = $this->createOrganizer();
        Mail::fake();
    }

    /** @test */
    public function organizer_can_view_event_invitations()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        EventInvitation::factory()->create([
            'event_details_id' => $event->id,
            'email' => 'invited@example.com',
        ]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/invitations");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Invitations.Index');
        $response->assertViewHas('invitations');
    }

    /** @test */
    public function organizer_cannot_view_invitations_for_others_event()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/invitations");

        $response->assertForbidden();
    }

    /** @test */
    public function organizer_can_send_invitation()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $invitationData = [
            'event_details_id' => $event->id,
            'email' => 'newplayer@example.com',
        ];

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/invitations', $invitationData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_invitations', [
            'event_details_id' => $event->id,
            'email' => 'newplayer@example.com',
        ]);

        Mail::assertQueued(EventInvitationMail::class);
    }

    /** @test */
    public function organizer_cannot_send_invitation_to_same_email_twice()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        EventInvitation::factory()->create([
            'event_details_id' => $event->id,
            'email' => 'existing@example.com',
        ]);

        $invitationData = [
            'event_details_id' => $event->id,
            'email' => 'existing@example.com',
        ];

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/invitations', $invitationData);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Should still only have one invitation
        $this->assertEquals(1, EventInvitation::where('email', 'existing@example.com')->count());
    }

    /** @test */
    public function organizer_can_delete_invitation()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $invitation = EventInvitation::factory()->create([
            'event_details_id' => $event->id,
            'email' => 'todelete@example.com',
        ]);

        $response = $this->actingAs($this->organizer)
            ->delete("/organizer/invitations/{$invitation->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('event_invitations', ['id' => $invitation->id]);
    }

    /** @test */
    public function organizer_cannot_delete_others_invitation()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);

        $invitation = EventInvitation::factory()->create([
            'event_details_id' => $event->id,
        ]);

        $response = $this->actingAs($this->organizer)
            ->delete("/organizer/invitations/{$invitation->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('event_invitations', ['id' => $invitation->id]);
    }

    /** @test */
    public function it_validates_email_format_on_invitation()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/invitations', [
                'event_details_id' => $event->id,
                'email' => 'not-an-email',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_validates_required_fields_on_invitation()
    {
        $response = $this->actingAs($this->organizer)
            ->post('/organizer/invitations', []);

        $response->assertSessionHasErrors(['event_details_id', 'email']);
    }

    /** @test */
    public function guest_cannot_access_invitation_pages()
    {
        $event = $this->createEvent();

        $this->get("/organizer/events/{$event->id}/invitations")
            ->assertRedirect('/login');

        $this->post('/organizer/invitations', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function participant_cannot_access_organizer_invitations()
    {
        $participant = $this->createParticipant();
        $event = $this->createEvent();

        $this->actingAs($participant)
            ->get("/organizer/events/{$event->id}/invitations")
            ->assertForbidden();
    }

    /** @test */
    public function invitation_email_contains_event_details()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventName' => 'Special Tournament',
        ]);

        $this->actingAs($this->organizer)
            ->post('/organizer/invitations', [
                'event_details_id' => $event->id,
                'email' => 'player@example.com',
            ]);

        Mail::assertQueued(EventInvitationMail::class, function ($mail) use ($event) {
            return $mail->hasTo('player@example.com');
        });
    }

    /** @test */
    public function organizer_can_send_multiple_invitations_to_different_emails()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $emails = ['player1@example.com', 'player2@example.com', 'player3@example.com'];

        foreach ($emails as $email) {
            $this->actingAs($this->organizer)
                ->post('/organizer/invitations', [
                    'event_details_id' => $event->id,
                    'email' => $email,
                ]);
        }

        $this->assertEquals(3, EventInvitation::where('event_details_id', $event->id)->count());
        Mail::assertQueued(EventInvitationMail::class, 3);
    }
}
