<?php

namespace Tests\Unit\Notification;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{NotificationCounter, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;

class NotificationCounterModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $this->assertInstanceOf(User::class, $user->notificationCounter->user);
        $this->assertEquals($user->id, $user->notificationCounter->user->id);
    }

    /** @test */
    public function it_casts_counts_to_integer()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;

        $counter->update([
            'social_count' => 5,
            'teams_count' => 10,
            'event_count' => 3,
        ]);

        $fresh = $counter->fresh();
        $this->assertIsInt($fresh->social_count);
        $this->assertIsInt($fresh->teams_count);
        $this->assertIsInt($fresh->event_count);
    }

    /** @test */
    public function it_increments_social_counter()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;
        $counter->update(['social_count' => 5]);

        $counter->incrementCounter('social');

        $this->assertEquals(6, $counter->fresh()->social_count);
    }

    /** @test */
    public function it_increments_teams_counter()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;
        $counter->update(['teams_count' => 3]);

        $counter->incrementCounter('teams');

        $this->assertEquals(4, $counter->fresh()->teams_count);
    }

    /** @test */
    public function it_increments_event_counter()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;
        $counter->update(['event_count' => 2]);

        $counter->incrementCounter('event');

        $this->assertEquals(3, $counter->fresh()->event_count);
    }

    /** @test */
    public function it_decrements_social_counter()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;
        $counter->update(['social_count' => 5]);

        $counter->decrementCounter('social');

        $this->assertEquals(4, $counter->fresh()->social_count);
    }

    /** @test */
    public function it_decrements_teams_counter()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;
        $counter->update(['teams_count' => 10]);

        $counter->decrementCounter('teams');

        $this->assertEquals(9, $counter->fresh()->teams_count);
    }

    /** @test */
    public function it_does_not_decrement_below_zero()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;
        $counter->update(['social_count' => 0]);

        $counter->decrementCounter('social');

        $this->assertEquals(0, $counter->fresh()->social_count);
    }

    /** @test */
    public function it_clears_cache_after_increment()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;

        Cache::shouldReceive('forget')
            ->once()
            ->with("notification_count_{$user->id}");

        $counter->incrementCounter('social');
    }

    /** @test */
    public function it_clears_cache_after_decrement()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;
        $counter->update(['social_count' => 5]);

        Cache::shouldReceive('forget')
            ->once()
            ->with("notification_count_{$user->id}");

        $counter->decrementCounter('social');
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $counter = $user->notificationCounter;

        $counter->update([
            'social_count' => 15,
            'teams_count' => 8,
            'event_count' => 12,
        ]);

        $fresh = $counter->fresh();
        $this->assertEquals(15, $fresh->social_count);
        $this->assertEquals(8, $fresh->teams_count);
        $this->assertEquals(12, $fresh->event_count);
    }

    /** @test */
    public function it_initializes_with_zero_counts()
    {
        $user = User::factory()->create();
        $counter = NotificationCounter::factory()->create([
            'user_id' => $user->id,
            'social_count' => 0,
            'teams_count' => 0,
            'event_count' => 0,
        ]);

        $this->assertEquals(0, $counter->social_count);
        $this->assertEquals(0, $counter->teams_count);
        $this->assertEquals(0, $counter->event_count);
    }
}
