<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Friend, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FriendModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user1()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $friend = Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
        ]);

        $this->assertInstanceOf(User::class, $friend->user1);
        $this->assertEquals($user1->id, $friend->user1->id);
    }

    /** @test */
    public function it_belongs_to_user2()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $friend = Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
        ]);

        $this->assertInstanceOf(User::class, $friend->user2);
        $this->assertEquals($user2->id, $friend->user2->id);
    }

    /** @test */
    public function it_belongs_to_actor()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $friend = Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'actor_id' => $user1->id,
        ]);

        $this->assertInstanceOf(User::class, $friend->actor);
        $this->assertEquals($user1->id, $friend->actor->id);
    }

    /** @test */
    public function it_stores_friendship_status()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $friend = Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $friend->status);
    }

    /** @test */
    public function it_checks_friendship_between_two_users()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted',
        ]);

        $friendship = Friend::checkFriendship($user1->id, $user2->id);

        $this->assertInstanceOf(Friend::class, $friendship);
    }

    /** @test */
    public function it_checks_friendship_in_reverse_order()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted',
        ]);

        $friendship = Friend::checkFriendship($user2->id, $user1->id);

        $this->assertInstanceOf(Friend::class, $friendship);
    }

    /** @test */
    public function it_returns_null_when_no_friendship_exists()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $friendship = Friend::checkFriendship($user1->id, $user2->id);

        $this->assertNull($friendship);
    }

    /** @test */
    public function it_gets_friend_count_for_user()
    {
        $user = $this->createParticipant();
        $friend1 = $this->createParticipant();
        $friend2 = $this->createParticipant();

        Friend::factory()->create([
            'user1_id' => $user->id,
            'user2_id' => $friend1->id,
            'status' => 'accepted',
        ]);

        Friend::factory()->create([
            'user1_id' => $friend2->id,
            'user2_id' => $user->id,
            'status' => 'accepted',
        ]);

        Friend::factory()->create([
            'user1_id' => $user->id,
            'user2_id' => $this->createParticipant()->id,
            'status' => 'pending',
        ]);

        $count = Friend::getFriendCount($user->id);

        $this->assertEquals(2, $count);
    }

    /** @test */
    public function it_tracks_different_friendship_statuses()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $statuses = ['pending', 'accepted', 'rejected', 'blocked'];

        foreach ($statuses as $status) {
            $friend = Friend::factory()->create([
                'user1_id' => $user1->id,
                'user2_id' => $user2->id,
                'status' => $status,
            ]);
            $this->assertEquals($status, $friend->status);
        }
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $friend = Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted',
            'actor_id' => $user1->id,
        ]);

        $this->assertEquals($user1->id, $friend->user1_id);
        $this->assertEquals($user2->id, $friend->user2_id);
        $this->assertEquals('accepted', $friend->status);
        $this->assertEquals($user1->id, $friend->actor_id);
    }

    /** @test */
    public function it_can_update_friendship_status()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $friend = Friend::factory()->create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'pending',
        ]);

        $friend->update(['status' => 'accepted']);

        $this->assertEquals('accepted', $friend->fresh()->status);
    }
}
