<?php

namespace Tests\Unit\Team;

use Tests\TestCase;
use App\Models\RosterCaptain;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RosterCaptainModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $captain = RosterCaptain::create([
            'team_member_id' => 1,
            'join_events_id' => 1,
            'teams_id' => 1,
        ]);

        $this->assertEquals(1, $captain->team_member_id);
        $this->assertEquals(1, $captain->join_events_id);
        $this->assertEquals(1, $captain->teams_id);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $captain = new RosterCaptain();

        $this->assertFalse($captain->timestamps);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $captain = new RosterCaptain();

        $this->assertEquals('rosters_captain', $captain->getTable());
    }

    /** @test */
    public function it_can_create_multiple_captains()
    {
        RosterCaptain::create([
            'team_member_id' => 1,
            'join_events_id' => 1,
            'teams_id' => 1,
        ]);

        RosterCaptain::create([
            'team_member_id' => 2,
            'join_events_id' => 2,
            'teams_id' => 2,
        ]);

        $this->assertEquals(2, RosterCaptain::count());
    }
}
