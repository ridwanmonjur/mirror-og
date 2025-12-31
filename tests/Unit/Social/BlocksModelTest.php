<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Blocks, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BlocksModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $blockedUser = $this->createParticipant();

        $block = Blocks::create([
            'user_id' => $user->id,
            'blocked_user_id' => $blockedUser->id,
        ]);

        $this->assertInstanceOf(User::class, $block->user);
        $this->assertEquals($user->id, $block->user->id);
    }

    /** @test */
    public function it_belongs_to_blocked_user()
    {
        $user = $this->createParticipant();
        $blockedUser = $this->createParticipant();

        $block = Blocks::create([
            'user_id' => $user->id,
            'blocked_user_id' => $blockedUser->id,
        ]);

        $this->assertInstanceOf(User::class, $block->blockedUser);
        $this->assertEquals($blockedUser->id, $block->blockedUser->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $blockedUser = $this->createParticipant();

        $block = Blocks::create([
            'user_id' => $user->id,
            'blocked_user_id' => $blockedUser->id,
        ]);

        $this->assertEquals($user->id, $block->user_id);
        $this->assertEquals($blockedUser->id, $block->blocked_user_id);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $block = new Blocks();

        $this->assertEquals('blocks', $block->getTable());
    }

    /** @test */
    public function user_can_block_multiple_users()
    {
        $user = $this->createParticipant();
        $blockedUser1 = $this->createParticipant();
        $blockedUser2 = $this->createParticipant();

        Blocks::create([
            'user_id' => $user->id,
            'blocked_user_id' => $blockedUser1->id,
        ]);

        Blocks::create([
            'user_id' => $user->id,
            'blocked_user_id' => $blockedUser2->id,
        ]);

        $blocks = Blocks::where('user_id', $user->id)->get();

        $this->assertCount(2, $blocks);
    }

    /** @test */
    public function user_can_be_blocked_by_multiple_users()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $blockedUser = $this->createParticipant();

        Blocks::create([
            'user_id' => $user1->id,
            'blocked_user_id' => $blockedUser->id,
        ]);

        Blocks::create([
            'user_id' => $user2->id,
            'blocked_user_id' => $blockedUser->id,
        ]);

        $blocks = Blocks::where('blocked_user_id', $blockedUser->id)->get();

        $this->assertCount(2, $blocks);
    }
}
