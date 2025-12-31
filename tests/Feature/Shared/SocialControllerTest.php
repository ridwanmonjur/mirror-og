<?php

namespace Tests\Feature\Shared;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams};
use App\Models\{OrganizerFollow, ParticipantFollow, Friend, Stars, Report, Blocks};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SocialControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
    }

    /** @test */
    public function participant_can_follow_organizer()
    {
        $organizer = $this->createOrganizer();

        $response = $this->actingAs($this->participant)
            ->post('/social/follow-organizer', [
                'organizer_id' => $organizer->organizer->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('organizer_follows', [
            'user_id' => $this->participant->id,
            'organizer_id' => $organizer->organizer->id,
        ]);
    }

    /** @test */
    public function participant_can_unfollow_organizer()
    {
        $organizer = $this->createOrganizer();

        OrganizerFollow::factory()->create([
            'user_id' => $this->participant->id,
            'organizer_id' => $organizer->organizer->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/social/follow-organizer', [
                'organizer_id' => $organizer->organizer->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('organizer_follows', [
            'user_id' => $this->participant->id,
            'organizer_id' => $organizer->organizer->id,
        ]);
    }

    /** @test */
    public function participant_can_send_friend_request()
    {
        $otherParticipant = $this->createParticipant();

        $response = $this->actingAs($this->participant)
            ->post('/social/friend', [
                'friend_id' => $otherParticipant->id,
                'action' => 'send',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('friends', [
            'user_id' => $this->participant->id,
            'friend_id' => $otherParticipant->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function participant_can_accept_friend_request()
    {
        $requester = $this->createParticipant();

        Friend::factory()->create([
            'user_id' => $requester->id,
            'friend_id' => $this->participant->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/social/friend', [
                'friend_id' => $requester->id,
                'action' => 'accept',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('friends', [
            'user_id' => $requester->id,
            'friend_id' => $this->participant->id,
            'status' => 'accepted',
        ]);
    }

    /** @test */
    public function participant_can_reject_friend_request()
    {
        $requester = $this->createParticipant();

        $friendship = Friend::factory()->create([
            'user_id' => $requester->id,
            'friend_id' => $this->participant->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->post('/social/friend', [
                'friend_id' => $requester->id,
                'action' => 'reject',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('friends', [
            'id' => $friendship->id,
        ]);
    }

    /** @test */
    public function participant_can_follow_another_participant()
    {
        $otherParticipant = $this->createParticipant();

        $response = $this->actingAs($this->participant)
            ->post('/social/follow-participant', [
                'participant_id' => $otherParticipant->participant->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('participant_follows', [
            'user_id' => $this->participant->id,
            'participant_id' => $otherParticipant->participant->id,
        ]);
    }

    /** @test */
    public function participant_can_view_connections()
    {
        $response = $this->actingAs($this->participant)
            ->get("/social/{$this->participant->id}/connections");

        $response->assertStatus(200);
        $response->assertViewHas('friends');
        $response->assertViewHas('followers');
    }

    /** @test */
    public function participant_can_toggle_star_on_team()
    {
        $team = $this->createTeam();

        $response = $this->actingAs($this->participant)
            ->post("/social/{$team->id}/star");

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('stars', [
            'user_id' => $this->participant->id,
            'starable_id' => $team->id,
            'starable_type' => get_class($team),
        ]);
    }

    /** @test */
    public function participant_can_remove_star_from_team()
    {
        $team = $this->createTeam();

        Stars::factory()->create([
            'user_id' => $this->participant->id,
            'starable_id' => $team->id,
            'starable_type' => get_class($team),
        ]);

        $response = $this->actingAs($this->participant)
            ->post("/social/{$team->id}/star");

        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('stars', [
            'user_id' => $this->participant->id,
            'starable_id' => $team->id,
        ]);
    }

    /** @test */
    public function participant_can_report_user()
    {
        $reportedUser = $this->createParticipant();

        $reportData = [
            'reason' => 'Inappropriate behavior',
            'description' => 'Detailed description of the issue',
        ];

        $response = $this->actingAs($this->participant)
            ->post("/social/{$reportedUser->id}/report", $reportData);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->participant->id,
            'reported_id' => $reportedUser->id,
            'reason' => 'Inappropriate behavior',
        ]);
    }

    /** @test */
    public function participant_can_view_reports()
    {
        $response = $this->actingAs($this->participant)
            ->get("/social/{$this->participant->id}/reports");

        $response->assertStatus(200);
        $response->assertViewHas('reports');
    }

    /** @test */
    public function guest_cannot_access_social_features()
    {
        $this->post('/social/follow-organizer', [])->assertRedirect('/login');
        $this->post('/social/friend', [])->assertRedirect('/login');
        $this->post('/social/follow-participant', [])->assertRedirect('/login');
    }

    /** @test */
    public function participant_cannot_send_friend_request_to_self()
    {
        $response = $this->actingAs($this->participant)
            ->post('/social/friend', [
                'friend_id' => $this->participant->id,
                'action' => 'send',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
