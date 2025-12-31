<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{ActivityLogs, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivityLogsModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_stores_activity_action()
    {
        $user = $this->createParticipant();

        $log = ActivityLogs::factory()->create([
            'action' => 'team_joined',
            'subject_id' => $user->id,
            'subject_type' => User::class,
        ]);

        $this->assertEquals('team_joined', $log->action);
    }

    /** @test */
    public function it_stores_subject_polymorphic_relationship()
    {
        $user = $this->createParticipant();

        $log = ActivityLogs::factory()->create([
            'subject_id' => $user->id,
            'subject_type' => User::class,
        ]);

        $this->assertEquals($user->id, $log->subject_id);
        $this->assertEquals(User::class, $log->subject_type);
    }

    /** @test */
    public function it_stores_object_polymorphic_relationship()
    {
        $user = $this->createParticipant();

        $log = ActivityLogs::factory()->create([
            'object_id' => $user->id,
            'object_type' => User::class,
        ]);

        $this->assertEquals($user->id, $log->object_id);
        $this->assertEquals(User::class, $log->object_type);
    }

    /** @test */
    public function it_stores_log_data()
    {
        $log = ActivityLogs::factory()->create([
            'log' => 'User joined a new team',
        ]);

        $this->assertEquals('User joined a new team', $log->log);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $log = ActivityLogs::factory()->create([
            'action' => 'event_created',
            'subject_id' => $user->id,
            'subject_type' => User::class,
            'object_id' => 123,
            'object_type' => 'App\Models\EventDetail',
            'log' => 'User created an event',
        ]);

        $this->assertEquals('event_created', $log->action);
        $this->assertEquals($user->id, $log->subject_id);
        $this->assertEquals(User::class, $log->subject_type);
        $this->assertEquals(123, $log->object_id);
        $this->assertEquals('App\Models\EventDetail', $log->object_type);
        $this->assertEquals('User created an event', $log->log);
    }
}
