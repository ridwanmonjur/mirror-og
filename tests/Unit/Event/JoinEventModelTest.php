<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams, CreatesTestPayments};
use App\Models\{JoinEvent, EventDetail, Team, User, RosterMember, ParticipantPayment, EventJoinResults, TeamMember};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JoinEventModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams, CreatesTestPayments;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $joinEvent = JoinEvent::factory()->create([
            'joiner_id' => $user->id,
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(User::class, $joinEvent->user);
        $this->assertEquals($user->id, $joinEvent->user->id);
    }

    /** @test */
    public function it_belongs_to_event_details()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $joinEvent->eventDetails);
        $this->assertEquals($event->id, $joinEvent->eventDetails->id);
    }

    /** @test */
    public function it_belongs_to_team()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(Team::class, $joinEvent->team);
        $this->assertEquals($team->id, $joinEvent->team->id);
    }

    /** @test */
    public function it_has_roster_members_relationship()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $rosterMember = RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
        ]);

        $joinEvent->refresh();
        $this->assertCount(1, $joinEvent->roster);
        $this->assertInstanceOf(RosterMember::class, $joinEvent->roster->first());
    }

    /** @test */
    public function it_has_payments_relationship()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_amount' => 50.00,
        ]);

        $joinEvent->refresh();
        $this->assertCount(1, $joinEvent->payments);
        $this->assertInstanceOf(ParticipantPayment::class, $joinEvent->payments->first());
    }

    /** @test */
    public function it_has_results_relationship()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        EventJoinResults::factory()->create([
            'join_events_id' => $joinEvent->id,
            'position' => 1,
        ]);

        $joinEvent->refresh();
        $this->assertCount(1, $joinEvent->results);
        $this->assertInstanceOf(EventJoinResults::class, $joinEvent->results->first());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'approved',
            'payment_status' => 'completed',
            'vote_ongoing' => false,
        ]);

        $this->assertEquals('approved', $joinEvent->join_status);
        $this->assertEquals('completed', $joinEvent->payment_status);
        $this->assertFalse($joinEvent->vote_ongoing);
    }

    /** @test */
    public function it_can_save_join_event_with_static_method()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $data = [
            'team_id' => $team->id,
            'joiner_id' => $user->id,
            'joiner_participant_id' => $user->participant->id,
            'event_details_id' => $event->id,
        ];

        $joinEvent = JoinEvent::saveJoinEvent($data);

        $this->assertInstanceOf(JoinEvent::class, $joinEvent);
        $this->assertEquals($team->id, $joinEvent->team_id);
        $this->assertEquals($user->id, $joinEvent->joiner_id);
        $this->assertEquals($event->id, $joinEvent->event_details_id);
    }

    /** @test */
    public function it_completes_payment_and_updates_status()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'payment_status' => 'pending',
        ]);

        $joinEvent->completePayment('normal');

        $this->assertEquals('completed', $joinEvent->payment_status);
        $this->assertEquals('normal', $joinEvent->register_time);
    }

    /** @test */
    public function it_calculates_leave_vote_with_majority_leave()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'vote_ongoing' => true,
            'join_status' => 'approved',
        ]);

        // Create 3 roster members: 2 vote to leave, 1 votes to stay
        RosterMember::factory()->create(['join_events_id' => $joinEvent->id, 'vote_to_quit' => 1]);
        RosterMember::factory()->create(['join_events_id' => $joinEvent->id, 'vote_to_quit' => 1]);
        RosterMember::factory()->create(['join_events_id' => $joinEvent->id, 'vote_to_quit' => 0]);

        $joinEvent->refresh();
        [$leaveRatio, $stayRatio] = $joinEvent->decideRosterLeaveVote();

        $this->assertEquals(2/3, $leaveRatio);
        $this->assertEquals(1/3, $stayRatio);
        $this->assertFalse($joinEvent->vote_ongoing);
        $this->assertEquals('canceled', $joinEvent->join_status);
    }

    /** @test */
    public function it_calculates_leave_vote_with_majority_stay()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'vote_ongoing' => true,
            'join_status' => 'approved',
        ]);

        // Create 3 roster members: 1 votes to leave, 2 vote to stay
        RosterMember::factory()->create(['join_events_id' => $joinEvent->id, 'vote_to_quit' => 1]);
        RosterMember::factory()->create(['join_events_id' => $joinEvent->id, 'vote_to_quit' => 0]);
        RosterMember::factory()->create(['join_events_id' => $joinEvent->id, 'vote_to_quit' => 0]);

        $joinEvent->refresh();
        [$leaveRatio, $stayRatio] = $joinEvent->decideRosterLeaveVote();

        $this->assertEquals(1/3, $leaveRatio);
        $this->assertEquals(2/3, $stayRatio);
        $this->assertFalse($joinEvent->vote_ongoing);
        $this->assertEquals('approved', $joinEvent->join_status); // Should stay approved
    }

    /** @test */
    public function it_checks_if_user_is_part_of_roster()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'approved',
        ]);

        RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $user->id,
        ]);

        $isPartOfRoster = JoinEvent::isPartOfRoster($event->id, $user->id);

        $this->assertTrue($isPartOfRoster);
    }

    /** @test */
    public function it_returns_false_for_user_not_in_roster()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();

        $isPartOfRoster = JoinEvent::isPartOfRoster($event->id, $user->id);

        $this->assertFalse($isPartOfRoster);
    }

    /** @test */
    public function it_gets_joined_teams_for_same_event()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        RosterMember::factory()->create([
            'join_events_id' => $joinEvent->id,
            'user_id' => $user->id,
        ]);

        $result = JoinEvent::getJoinedByTeamsForSameEvent($event->id, $user->id);

        $this->assertInstanceOf(JoinEvent::class, $result);
        $this->assertEquals($joinEvent->id, $result->id);
    }

    /** @test */
    public function it_returns_null_when_no_user_for_same_event()
    {
        $event = $this->createEvent();

        $result = JoinEvent::getJoinedByTeamsForSameEvent($event->id, null);

        $this->assertNull($result);
    }

    /** @test */
    public function it_tracks_different_join_statuses()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $statuses = ['pending', 'approved', 'canceled', 'rejected'];

        foreach ($statuses as $status) {
            $joinEvent = JoinEvent::factory()->create([
                'event_details_id' => $event->id,
                'team_id' => $team->id,
                'join_status' => $status,
            ]);
            $this->assertEquals($status, $joinEvent->join_status);
        }
    }

    /** @test */
    public function it_tracks_different_payment_statuses()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $statuses = ['pending', 'completed', 'failed', 'refunded'];

        foreach ($statuses as $status) {
            $joinEvent = JoinEvent::factory()->create([
                'event_details_id' => $event->id,
                'team_id' => $team->id,
                'payment_status' => $status,
            ]);
            $this->assertEquals($status, $joinEvent->payment_status);
        }
    }

    /** @test */
    public function it_belongs_to_vote_starter()
    {
        $voteStarter = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam();

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'vote_starter_id' => $voteStarter->id,
        ]);

        $this->assertInstanceOf(User::class, $joinEvent->voteStarter);
        $this->assertEquals($voteStarter->id, $joinEvent->voteStarter->id);
    }
}
