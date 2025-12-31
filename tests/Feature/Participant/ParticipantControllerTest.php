<?php

namespace Tests\Feature\Participant;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\ActivityLogs;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
    }

    /** @test */
    public function participant_can_view_own_profile()
    {
        $response = $this->actingAs($this->participant)
            ->get('/participant/profile');

        $response->assertStatus(200);
        $response->assertViewIs('Participant.Profile.Show');
        $response->assertViewHas('participant');
    }

    /** @test */
    public function participant_can_view_another_participants_profile()
    {
        $otherParticipant = $this->createParticipant();

        $response = $this->actingAs($this->participant)
            ->get("/participant/profile/{$otherParticipant->participant->id}");

        $response->assertStatus(200);
        $response->assertViewIs('Participant.Profile.Show');
        $response->assertViewHas('participant');
    }

    /** @test */
    public function participant_can_update_own_profile()
    {
        $updateData = [
            'bio' => 'Updated bio',
            'region' => 'SEA',
            'country' => 'Malaysia',
        ];

        $response = $this->actingAs($this->participant)
            ->put('/participant/profile', $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('participants', [
            'user_id' => $this->participant->id,
            'bio' => 'Updated bio',
        ]);
    }

    /** @test */
    public function participant_can_search_for_other_participants()
    {
        $participant1 = $this->createParticipant(['name' => 'PlayerOne']);
        $participant2 = $this->createParticipant(['name' => 'PlayerTwo']);

        $response = $this->actingAs($this->participant)
            ->get('/participant/search?query=PlayerOne');

        $response->assertStatus(200);
        $response->assertViewHas('participants');
    }

    /** @test */
    public function participant_can_view_activity_logs()
    {
        ActivityLogs::factory()->create([
            'subject_id' => $this->participant->id,
            'subject_type' => get_class($this->participant),
            'action' => 'team_joined',
        ]);

        $response = $this->actingAs($this->participant)
            ->get("/participant/{$this->participant->id}/activity-logs");

        $response->assertStatus(200);
        $response->assertViewHas('logs');
    }

    /** @test */
    public function participant_can_view_others_activity_logs()
    {
        $otherParticipant = $this->createParticipant();

        ActivityLogs::factory()->create([
            'subject_id' => $otherParticipant->id,
            'subject_type' => get_class($otherParticipant),
            'action' => 'event_joined',
        ]);

        $response = $this->actingAs($this->participant)
            ->get("/participant/{$otherParticipant->id}/activity-logs");

        $response->assertStatus(200);
        $response->assertViewHas('logs');
    }

    /** @test */
    public function guest_cannot_access_participant_profile_pages()
    {
        $this->get('/participant/profile')
            ->assertRedirect('/login');

        $this->put('/participant/profile', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function organizer_cannot_access_participant_profile_pages()
    {
        $organizer = $this->createOrganizer();

        $this->actingAs($organizer)
            ->get('/participant/profile')
            ->assertForbidden();

        $this->actingAs($organizer)
            ->put('/participant/profile', [])
            ->assertForbidden();
    }

    /** @test */
    public function it_validates_required_fields_on_profile_update()
    {
        $response = $this->actingAs($this->participant)
            ->put('/participant/profile', []);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function viewing_nonexistent_participant_profile_returns_404()
    {
        $response = $this->actingAs($this->participant)
            ->get('/participant/profile/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function search_returns_empty_when_no_matches()
    {
        $response = $this->actingAs($this->participant)
            ->get('/participant/search?query=NonexistentPlayer');

        $response->assertStatus(200);
        $response->assertViewHas('participants');
    }
}
