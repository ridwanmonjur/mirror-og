<?php

namespace Tests\Unit\Team;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestTeams};
use App\Models\{TeamCaptain, Team, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TeamCaptainModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestTeams;

    /** @test */
    public function it_belongs_to_team()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam([], $user);

        $this->assertInstanceOf(Team::class, $team->captain->team);
    }

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam([], $user);

        $this->assertInstanceOf(User::class, $team->captain->user);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $captain = new TeamCaptain();
        $this->assertFalse($captain->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();

        $captain = TeamCaptain::factory()->create([
            'user_id' => $user->id,
            'teams_id' => $team->id,
        ]);

        $this->assertEquals($user->id, $captain->user_id);
        $this->assertEquals($team->id, $captain->teams_id);
    }

    /** @test */
    public function team_can_have_one_captain()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam([], $user);

        $captains = TeamCaptain::where('teams_id', $team->id)->get();

        $this->assertCount(1, $captains);
        $this->assertEquals($user->id, $captains->first()->user_id);
    }

    /** @test */
    public function it_can_change_team_captain()
    {
        $oldCaptain = $this->createParticipant();
        $newCaptain = $this->createParticipant();
        $team = $this->createTeam([], $oldCaptain);

        $captain = $team->captain;
        $captain->update(['user_id' => $newCaptain->id]);

        $this->assertEquals($newCaptain->id, $captain->fresh()->user_id);
    }
}
