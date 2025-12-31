<?php

namespace Tests\Unit\Team;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestTeams};
use App\Models\{Team, TeamMember, TeamCaptain, TeamProfile, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TeamModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestTeams;

    /** @test */
    public function it_belongs_to_creator()
    {
        $team = $this->createTeam();

        $this->assertInstanceOf(User::class, $team->user);
    }

    /** @test */
    public function it_has_captain_relationship()
    {
        $team = $this->createTeam();

        $this->assertInstanceOf(TeamCaptain::class, $team->captain);
    }

    /** @test */
    public function it_has_members_relationship()
    {
        $team = $this->createTeamWithMembers(3);

        $this->assertCount(3, $team->members);
        $this->assertInstanceOf(TeamMember::class, $team->members->first());
    }

    /** @test */
    public function it_has_profile_relationship()
    {
        $team = $this->createTeam();

        $this->assertInstanceOf(TeamProfile::class, $team->profile);
    }

    /** @test */
    public function it_scopes_paginated_search_by_name()
    {
        Team::factory()->create(['teamName' => 'Alpha Team']);
        Team::factory()->create(['teamName' => 'Beta Squad']);
        Team::factory()->create(['teamName' => 'Alpha Warriors']);

        $results = Team::paginatedSearch('Alpha')->get();

        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_limits_search_results()
    {
        Team::factory()->count(10)->create();

        $results = Team::paginatedSearch(null, null, 5)->get();

        $this->assertCount(5, $results);
    }

    /** @test */
    public function it_has_member_limit_attribute()
    {
        $team = $this->createTeam(['member_limit' => 10]);

        $this->assertEquals(10, $team->member_limit);
    }

    /** @test */
    public function it_creates_team_with_captain()
    {
        $creator = $this->createParticipant();
        $team = $this->createTeam([], $creator);

        $this->assertEquals($creator->id, $team->captain->user_id);
        $this->assertEquals($team->id, $team->captain->teams_id);
    }

    /** @test */
    public function it_can_add_members()
    {
        $team = $this->createTeam();
        $member = $this->createParticipant();

        $this->addTeamMember($team, $member);

        $team->refresh();
        $this->assertCount(1, $team->members);
    }

    /** @test */
    public function it_tracks_pending_members()
    {
        $team = $this->createTeamWithPendingMembers(2);

        $pendingCount = $team->members->where('status', 'pending')->count();

        $this->assertEquals(2, $pendingCount);
    }

    /** @test */
    public function it_tracks_approved_members()
    {
        $team = $this->createTeamWithMembers(3);

        $approvedCount = $team->members->where('status', 'approved')->count();

        $this->assertEquals(3, $approvedCount);
    }

    /** @test */
    public function it_respects_member_limit()
    {
        $team = $this->createFullTeam(5);

        // 5 limit = 1 captain + 4 members
        $this->assertEquals(4, $team->members->count());
        $this->assertEquals(5, $team->member_limit);
    }
}
