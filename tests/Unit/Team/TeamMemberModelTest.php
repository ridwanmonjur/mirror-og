<?php

namespace Tests\Unit\Team;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestTeams, CreatesTestPayments};
use App\Models\{TeamMember, Team, User, ParticipantPayment};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TeamMemberModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestTeams, CreatesTestPayments;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(User::class, $member->user);
        $this->assertEquals($user->id, $member->user->id);
    }

    /** @test */
    public function it_belongs_to_team()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(Team::class, $member->team);
        $this->assertEquals($team->id, $member->team->id);
    }

    /** @test */
    public function it_has_payments_relationship()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        ParticipantPayment::factory()->create([
            'team_members_id' => $member->id,
            'payment_amount' => 50.00,
        ]);

        $member->refresh();
        $this->assertCount(1, $member->payments);
        $this->assertInstanceOf(ParticipantPayment::class, $member->payments->first());
    }

    /** @test */
    public function it_tracks_member_status()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $member->status);

        $member->update(['status' => 'accepted']);
        $this->assertEquals('accepted', $member->fresh()->status);
    }

    /** @test */
    public function it_tracks_actor()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'actor' => 'captain',
        ]);

        $this->assertEquals('captain', $member->actor);
    }

    /** @test */
    public function it_checks_if_user_is_already_member()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $result = TeamMember::isAlreadyMember($team->id, $user->id);

        $this->assertCount(1, $result);
    }

    /** @test */
    public function it_returns_empty_collection_when_not_member()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $result = TeamMember::isAlreadyMember($team->id, $user->id);

        $this->assertCount(0, $result);
    }

    /** @test */
    public function it_gets_members_by_team_id_list()
    {
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();
        $user = $this->createParticipant();

        TeamMember::factory()->create(['team_id' => $team1->id, 'user_id' => $user->id]);
        TeamMember::factory()->create(['team_id' => $team2->id, 'user_id' => $user->id]);

        $results = TeamMember::getMembersByTeamIdList([$team1->id, $team2->id]);

        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_processes_team_members_by_status()
    {
        $team = $this->createTeam();

        TeamMember::factory()->create(['team_id' => $team->id, 'status' => 'accepted']);
        TeamMember::factory()->create(['team_id' => $team->id, 'status' => 'accepted']);
        TeamMember::factory()->create(['team_id' => $team->id, 'status' => 'pending']);
        TeamMember::factory()->create(['team_id' => $team->id, 'status' => 'rejected']);
        TeamMember::factory()->create(['team_id' => $team->id, 'status' => 'left']);

        $result = TeamMember::getProcessedTeamMembers($team->id);

        $this->assertEquals(2, $result['accepted']['count']);
        $this->assertEquals(1, $result['pending']['count']);
        $this->assertEquals(1, $result['rejected']['count']);
        $this->assertEquals(1, $result['left']['count']);
    }

    /** @test */
    public function it_bulk_creates_team_members()
    {
        $team = $this->createTeam();
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $user3 = $this->createParticipant();

        $userIds = [$user1->id, $user2->id, $user3->id];

        $result = TeamMember::bulkCreateTeanMembers($team->id, $userIds, 'pending');

        $this->assertTrue($result);
        $this->assertEquals(3, TeamMember::where('team_id', $team->id)->count());
    }

    /** @test */
    public function it_counts_accepted_team_members()
    {
        $team = $this->createTeam();

        $member = TeamMember::factory()->create([
            'team_id' => $team->id,
            'status' => 'accepted',
        ]);

        TeamMember::factory()->create(['team_id' => $team->id, 'status' => 'accepted']);
        TeamMember::factory()->create(['team_id' => $team->id, 'status' => 'pending']);

        $count = $member->countTeamMembers();

        $this->assertEquals(2, $count);
    }

    /** @test */
    public function it_returns_updated_at_diff_for_humans()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $diff = $member->updatedAtDiffForHumans();

        $this->assertIsString($diff);
        $this->assertStringContainsString('seconds ago', $diff);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $member = TeamMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'status' => 'accepted',
            'actor' => 'member',
        ]);

        $this->assertEquals($user->id, $member->user_id);
        $this->assertEquals($team->id, $member->team_id);
        $this->assertEquals('accepted', $member->status);
        $this->assertEquals('member', $member->actor);
    }

    /** @test */
    public function it_tracks_different_statuses()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $statuses = ['pending', 'accepted', 'rejected', 'left'];

        foreach ($statuses as $status) {
            $member = TeamMember::factory()->create([
                'user_id' => $user->id,
                'team_id' => $team->id,
                'status' => $status,
            ]);
            $this->assertEquals($status, $member->status);
        }
    }
}
