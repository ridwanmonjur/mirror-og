<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\{Achievements, EventDetail, JoinEvent, Team};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AchievementsModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_uses_correct_table_name()
    {
        $achievement = new Achievements();

        $this->assertEquals('achievements', $achievement->getTable());
    }

    /** @test */
    public function it_can_get_team_achievements()
    {
        $event = EventDetail::factory()->create();
        $team = Team::factory()->create();

        $joinEvent = JoinEvent::create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'confirmed',
        ]);

        Achievements::create([
            'join_event_id' => $joinEvent->id,
            'title' => 'First Place',
            'description' => 'Won the tournament',
        ]);

        $achievements = Achievements::getTeamAchievements($event->id);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $achievements);
    }

    /** @test */
    public function it_can_create_achievement()
    {
        $event = EventDetail::factory()->create();
        $team = Team::factory()->create();

        $joinEvent = JoinEvent::create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'confirmed',
        ]);

        $achievement = Achievements::create([
            'join_event_id' => $joinEvent->id,
            'title' => 'Champion',
            'description' => 'Tournament winner',
        ]);

        $this->assertDatabaseHas('achievements', [
            'join_event_id' => $joinEvent->id,
            'title' => 'Champion',
        ]);
    }
}
