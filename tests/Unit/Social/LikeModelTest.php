<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents};
use App\Models\{Like, User, EventDetail};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LikeModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();

        $like = Like::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->assertInstanceOf(User::class, $like->user);
        $this->assertEquals($user->id, $like->user->id);
    }

    /** @test */
    public function it_belongs_to_event()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();

        $like = Like::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $like->event);
        $this->assertEquals($event->id, $like->event->id);
    }

    /** @test */
    public function it_gets_likes_count()
    {
        $event = $this->createEvent();
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $user3 = $this->createParticipant();

        Like::factory()->create(['user_id' => $user1->id, 'event_id' => $event->id]);
        Like::factory()->create(['user_id' => $user2->id, 'event_id' => $event->id]);
        Like::factory()->create(['user_id' => $user3->id, 'event_id' => $event->id]);

        $count = Like::getLikesCount($event->id);

        $this->assertEquals(3, $count);
    }

    /** @test */
    public function it_checks_if_user_is_liking()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();

        Like::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $isLiking = Like::isLiking($user->id, $event->id);

        $this->assertTrue($isLiking);
    }

    /** @test */
    public function it_returns_false_when_not_liking()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();

        $isLiking = Like::isLiking($user->id, $event->id);

        $this->assertFalse($isLiking);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $event = $this->createEvent();

        $like = Like::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->assertEquals($user->id, $like->user_id);
        $this->assertEquals($event->id, $like->event_id);
    }

    /** @test */
    public function user_can_like_multiple_events()
    {
        $user = $this->createParticipant();
        $event1 = $this->createEvent();
        $event2 = $this->createEvent();

        Like::factory()->create(['user_id' => $user->id, 'event_id' => $event1->id]);
        Like::factory()->create(['user_id' => $user->id, 'event_id' => $event2->id]);

        $likes = Like::where('user_id', $user->id)->get();

        $this->assertCount(2, $likes);
    }

    /** @test */
    public function event_can_have_multiple_likes()
    {
        $event = $this->createEvent();
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $user3 = $this->createParticipant();

        Like::factory()->create(['user_id' => $user1->id, 'event_id' => $event->id]);
        Like::factory()->create(['user_id' => $user2->id, 'event_id' => $event->id]);
        Like::factory()->create(['user_id' => $user3->id, 'event_id' => $event->id]);

        $count = Like::getLikesCount($event->id);

        $this->assertEquals(3, $count);
    }
}
