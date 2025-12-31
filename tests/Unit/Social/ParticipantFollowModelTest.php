<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{ParticipantFollow, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantFollowModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_follower_user()
    {
        $follower = $this->createParticipant();
        $followee = $this->createParticipant();

        $follow = ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->followerUser);
        $this->assertEquals($follower->id, $follow->followerUser->id);
    }

    /** @test */
    public function it_belongs_to_followee_user()
    {
        $follower = $this->createParticipant();
        $followee = $this->createParticipant();

        $follow = ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->followeeUser);
        $this->assertEquals($followee->id, $follow->followeeUser->id);
    }

    /** @test */
    public function it_checks_if_follow_exists()
    {
        $follower = $this->createParticipant();
        $followee = $this->createParticipant();

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee->id,
        ]);

        $follow = ParticipantFollow::checkFollow($follower->id, $followee->id);

        $this->assertInstanceOf(ParticipantFollow::class, $follow);
    }

    /** @test */
    public function it_returns_null_when_no_follow_exists()
    {
        $follower = $this->createParticipant();
        $followee = $this->createParticipant();

        $follow = ParticipantFollow::checkFollow($follower->id, $followee->id);

        $this->assertNull($follow);
    }

    /** @test */
    public function it_gets_follower_count()
    {
        $user = $this->createParticipant();
        $follower1 = $this->createParticipant();
        $follower2 = $this->createParticipant();

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower1->id,
            'participant_followee' => $user->id,
        ]);

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower2->id,
            'participant_followee' => $user->id,
        ]);

        $count = ParticipantFollow::getFollowerCount($user->id);

        $this->assertEquals(2, $count);
    }

    /** @test */
    public function it_creates_follow_relationship()
    {
        $follower = $this->createParticipant();
        $followee = $this->createParticipant();

        $follow = ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee->id,
        ]);

        $this->assertEquals($follower->id, $follow->participant_follower);
        $this->assertEquals($followee->id, $follow->participant_followee);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $follower = $this->createParticipant();
        $followee = $this->createParticipant();

        $follow = ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee->id,
        ]);

        $this->assertEquals($follower->id, $follow->participant_follower);
        $this->assertEquals($followee->id, $follow->participant_followee);
    }

    /** @test */
    public function it_can_delete_follow_relationship()
    {
        $follower = $this->createParticipant();
        $followee = $this->createParticipant();

        $follow = ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee->id,
        ]);

        $followId = $follow->id;
        $follow->delete();

        $this->assertDatabaseMissing('participant_follows', ['id' => $followId]);
    }

    /** @test */
    public function multiple_users_can_follow_same_user()
    {
        $followee = $this->createParticipant();
        $follower1 = $this->createParticipant();
        $follower2 = $this->createParticipant();
        $follower3 = $this->createParticipant();

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower1->id,
            'participant_followee' => $followee->id,
        ]);

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower2->id,
            'participant_followee' => $followee->id,
        ]);

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower3->id,
            'participant_followee' => $followee->id,
        ]);

        $count = ParticipantFollow::getFollowerCount($followee->id);

        $this->assertEquals(3, $count);
    }

    /** @test */
    public function one_user_can_follow_multiple_users()
    {
        $follower = $this->createParticipant();
        $followee1 = $this->createParticipant();
        $followee2 = $this->createParticipant();

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee1->id,
        ]);

        ParticipantFollow::factory()->create([
            'participant_follower' => $follower->id,
            'participant_followee' => $followee2->id,
        ]);

        $follows = ParticipantFollow::where('participant_follower', $follower->id)->get();

        $this->assertCount(2, $follows);
    }
}
