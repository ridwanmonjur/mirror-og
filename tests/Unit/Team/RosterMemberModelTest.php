<?php

namespace Tests\Unit\Team;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestTeams, CreatesTestEvents};
use App\Models\{RosterMember, Team, User, JoinEvent, TeamMember};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;

class RosterMemberModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestTeams, CreatesTestEvents;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $roster = RosterMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'join_events_id' => $joinEvent->id,
        ]);

        $this->assertInstanceOf(User::class, $roster->user);
        $this->assertEquals($user->id, $roster->user->id);
    }

    /** @test */
    public function it_belongs_to_team()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $roster = RosterMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'join_events_id' => $joinEvent->id,
        ]);

        $this->assertInstanceOf(Team::class, $roster->team);
        $this->assertEquals($team->id, $roster->team->id);
    }

    /** @test */
    public function it_stores_vote_to_quit()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $event = $this->createEvent();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $roster = RosterMember::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'join_events_id' => $joinEvent->id,
            'vote_to_quit' => 1,
        ]);

        $this->assertEquals(1, $roster->vote_to_quit);
    }

    /** @test */
    public function it_clears_roster_cache()
    {
        $event = $this->createEvent();

        Cache::shouldReceive('forget')
            ->once()
            ->with("roster_data_event_{$event->id}");

        RosterMember::clearRosterCache($event->id);
    }

    /** @test */
    public function it_gets_members_by_team_id_list()
    {
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();
        $event = $this->createEvent();

        $joinEvent1 = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team1->id,
        ]);

        $joinEvent2 = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team2->id,
        ]);

        RosterMember::factory()->create(['join_events_id' => $joinEvent1->id]);
        RosterMember::factory()->create(['join_events_id' => $joinEvent2->id]);

        $results = RosterMember::getMembersByTeamIdList([$joinEvent1->id, $joinEvent2->id]);

        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_keys_roster_by_member_id()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $teamMember1 = TeamMember::factory()->create(['team_id' => $team->id]);
        $teamMember2 = TeamMember::factory()->create(['team_id' => $team->id]);

        $roster1 = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'team_member_id' => $teamMember1->id,
        ]);

        $roster2 = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'team_member_id' => $teamMember2->id,
        ]);

        $rosterMembers = collect([$roster1, $roster2]);
        $keyed = RosterMember::keyByMemberId($rosterMembers);

        $this->assertArrayHasKey($teamMember1->id, $keyed);
        $this->assertArrayHasKey($teamMember2->id, $keyed);
        $this->assertEquals($roster1->id, $keyed[$teamMember1->id]->id);
    }

    /** @test */
    public function it_counts_votes_for_leave()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $roster = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'vote_to_quit' => true,
        ]);

        [$stayCount, $leaveCount, $totalCount] = $roster->countVotes();

        $this->assertEquals(0, $stayCount);
        $this->assertEquals(1, $leaveCount);
        $this->assertEquals(1, $totalCount);
    }

    /** @test */
    public function it_counts_votes_for_stay()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $roster = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'vote_to_quit' => false,
        ]);

        [$stayCount, $leaveCount, $totalCount] = $roster->countVotes();

        $this->assertEquals(1, $stayCount);
        $this->assertEquals(0, $leaveCount);
        $this->assertEquals(1, $totalCount);
    }

    /** @test */
    public function it_gets_roster_vote_view_for_user()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $event = $this->createEvent();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $teamMember = TeamMember::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $roster = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $user->id,
            'team_member_id' => $teamMember->id,
            'vote_to_quit' => 1,
        ]);

        $currentUser = [];
        $roster->getRosterVoteView($user->id, $currentUser);

        $this->assertTrue($currentUser['isUserPartOfRoster']);
        $this->assertEquals($teamMember->id, $currentUser['memberId']);
        $this->assertEquals(1, $currentUser['vote_to_quit']);
        $this->assertEquals($roster->id, $currentUser['rosterId']);
    }

    /** @test */
    public function it_does_not_modify_current_user_for_different_user()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $team = $this->createTeam();
        $event = $this->createEvent();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $roster = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $user1->id,
        ]);

        $currentUser = [];
        $roster->getRosterVoteView($user2->id, $currentUser);

        $this->assertEmpty($currentUser);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $event = $this->createEvent();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $teamMember = TeamMember::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $roster = RosterMember::factory()->create([
            'user_id' => $user->id,
            'join_events_id' => $joinEvent->id,
            'team_id' => $team->id,
            'team_member_id' => $teamMember->id,
            'vote_to_quit' => 0,
        ]);

        $this->assertEquals($user->id, $roster->user_id);
        $this->assertEquals($joinEvent->id, $roster->join_events_id);
        $this->assertEquals($team->id, $roster->team_id);
        $this->assertEquals($teamMember->id, $roster->team_member_id);
        $this->assertEquals(0, $roster->vote_to_quit);
    }
}
