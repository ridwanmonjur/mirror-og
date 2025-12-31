<?php

namespace Tests\Feature\Participant;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestTeams};
use App\Models\{Team, TeamMember, TeamFollow};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantTeamControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestTeams;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
    }

    /** @test */
    public function participant_can_view_team_list()
    {
        $this->createTeam();
        $this->createTeam();

        $response = $this->actingAs($this->participant)
            ->get('/participant/teams');

        $response->assertStatus(200);
        $response->assertViewIs('Participant.Team.Index');
        $response->assertViewHas('teams');
    }

    /** @test */
    public function participant_can_view_team_details()
    {
        $team = $this->createTeam();

        $response = $this->actingAs($this->participant)
            ->get("/participant/teams/{$team->id}");

        $response->assertStatus(200);
        $response->assertViewIs('Participant.Team.Show');
        $response->assertViewHas('team');
    }

    /** @test */
    public function participant_can_follow_team()
    {
        $team = $this->createTeam();

        $response = $this->actingAs($this->participant)
            ->post("/participant/teams/{$team->id}/follow");

        $response->assertRedirect();

        $this->assertDatabaseHas('team_follows', [
            'user_id' => $this->participant->id,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function participant_can_unfollow_team()
    {
        $team = $this->createTeam();

        TeamFollow::factory()->create([
            'user_id' => $this->participant->id,
            'team_id' => $team->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->post("/participant/teams/{$team->id}/follow");

        $response->assertRedirect();

        $this->assertDatabaseMissing('team_follows', [
            'user_id' => $this->participant->id,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function participant_can_edit_own_team()
    {
        $team = $this->createTeam([], $this->participant);

        $updateData = [
            'teamName' => 'Updated Team Name',
            'teamTag' => 'UTN',
            'teamDescription' => 'Updated description',
        ];

        $response = $this->actingAs($this->participant)
            ->put('/participant/teams', $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'teamName' => 'Updated Team Name',
        ]);
    }

    /** @test */
    public function participant_cannot_edit_others_team()
    {
        $otherParticipant = $this->createParticipant();
        $team = $this->createTeam([], $otherParticipant);

        $updateData = ['teamName' => 'Hacked Name'];

        $response = $this->actingAs($this->participant)
            ->put('/participant/teams', $updateData);

        $response->assertForbidden();

        $this->assertDatabaseMissing('teams', [
            'id' => $team->id,
            'teamName' => 'Hacked Name',
        ]);
    }

    /** @test */
    public function participant_can_invite_member_to_team()
    {
        $team = $this->createTeam([], $this->participant);
        $invitee = $this->createParticipant();

        $response = $this->actingAs($this->participant)
            ->post("/participant/teams/{$team->id}/invite/{$invitee->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('team_members', [
            'user_id' => $invitee->id,
            'teams_id' => $team->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function participant_can_accept_team_invitation()
    {
        $team = $this->createTeam();

        $teamMember = TeamMember::factory()->create([
            'user_id' => $this->participant->id,
            'teams_id' => $team->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->post("/participant/teams/{$team->id}/pending-member", [
                'action' => 'accept',
            ]);

        $response->assertRedirect();

        $teamMember->refresh();
        $this->assertEquals('approved', $teamMember->status);
    }

    /** @test */
    public function participant_can_reject_team_invitation()
    {
        $team = $this->createTeam();

        $teamMember = TeamMember::factory()->create([
            'user_id' => $this->participant->id,
            'teams_id' => $team->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->post("/participant/teams/{$team->id}/reject-invitation", [
                'team_member_id' => $teamMember->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('team_members', [
            'id' => $teamMember->id,
        ]);
    }

    /** @test */
    public function participant_can_withdraw_team_invitation()
    {
        $team = $this->createTeam([], $this->participant);
        $invitee = $this->createParticipant();

        $teamMember = TeamMember::factory()->create([
            'user_id' => $invitee->id,
            'teams_id' => $team->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant)
            ->delete("/participant/teams/{$team->id}/withdraw-invitation", [
                'team_member_id' => $teamMember->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('team_members', [
            'id' => $teamMember->id,
        ]);
    }

    /** @test */
    public function guest_cannot_access_team_pages()
    {
        $team = $this->createTeam();

        $this->get('/participant/teams')
            ->assertRedirect('/login');

        $this->get("/participant/teams/{$team->id}")
            ->assertRedirect('/login');
    }

    /** @test */
    public function organizer_cannot_access_participant_team_pages()
    {
        $organizer = $this->createOrganizer();
        $team = $this->createTeam();

        $this->actingAs($organizer)
            ->get('/participant/teams')
            ->assertForbidden();
    }

    /** @test */
    public function it_validates_required_fields_on_team_edit()
    {
        $team = $this->createTeam([], $this->participant);

        $response = $this->actingAs($this->participant)
            ->put('/participant/teams', []);

        $response->assertSessionHasErrors();
    }
}
