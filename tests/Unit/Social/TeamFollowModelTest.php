<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestTeams};
use App\Models\{TeamFollow, Team, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TeamFollowModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestTeams;

    /** @test */
    public function it_belongs_to_team()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $follow = TeamFollow::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(Team::class, $follow->team);
        $this->assertEquals($team->id, $follow->team->id);
    }

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $follow = TeamFollow::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->user);
        $this->assertEquals($user->id, $follow->user->id);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $follow = new TeamFollow();
        $this->assertFalse($follow->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $follow = TeamFollow::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $this->assertEquals($user->id, $follow->user_id);
        $this->assertEquals($team->id, $follow->team_id);
    }

    /** @test */
    public function multiple_users_can_follow_same_team()
    {
        $team = $this->createTeam();
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $user3 = $this->createParticipant();

        TeamFollow::factory()->create(['user_id' => $user1->id, 'team_id' => $team->id]);
        TeamFollow::factory()->create(['user_id' => $user2->id, 'team_id' => $team->id]);
        TeamFollow::factory()->create(['user_id' => $user3->id, 'team_id' => $team->id]);

        $count = TeamFollow::where('team_id', $team->id)->count();

        $this->assertEquals(3, $count);
    }

    /** @test */
    public function user_can_follow_multiple_teams()
    {
        $user = $this->createParticipant();
        $team1 = $this->createTeam();
        $team2 = $this->createTeam();

        TeamFollow::factory()->create(['user_id' => $user->id, 'team_id' => $team1->id]);
        TeamFollow::factory()->create(['user_id' => $user->id, 'team_id' => $team2->id]);

        $count = TeamFollow::where('user_id', $user->id)->count();

        $this->assertEquals(2, $count);
    }
}
