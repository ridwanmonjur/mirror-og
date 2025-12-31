<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Stars, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StarsModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();
        $starredUser = $this->createParticipant();

        $star = Stars::create([
            'user_id' => $user->id,
            'starred_user_id' => $starredUser->id,
        ]);

        $this->assertInstanceOf(User::class, $star->user);
        $this->assertEquals($user->id, $star->user->id);
    }

    /** @test */
    public function it_belongs_to_starred_user()
    {
        $user = $this->createParticipant();
        $starredUser = $this->createParticipant();

        $star = Stars::create([
            'user_id' => $user->id,
            'starred_user_id' => $starredUser->id,
        ]);

        $this->assertInstanceOf(User::class, $star->starredUser);
        $this->assertEquals($starredUser->id, $star->starredUser->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $starredUser = $this->createParticipant();

        $star = Stars::create([
            'user_id' => $user->id,
            'starred_user_id' => $starredUser->id,
        ]);

        $this->assertEquals($user->id, $star->user_id);
        $this->assertEquals($starredUser->id, $star->starred_user_id);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $star = new Stars();

        $this->assertEquals('stars', $star->getTable());
    }

    /** @test */
    public function user_can_star_multiple_users()
    {
        $user = $this->createParticipant();
        $starredUser1 = $this->createParticipant();
        $starredUser2 = $this->createParticipant();

        Stars::create([
            'user_id' => $user->id,
            'starred_user_id' => $starredUser1->id,
        ]);

        Stars::create([
            'user_id' => $user->id,
            'starred_user_id' => $starredUser2->id,
        ]);

        $stars = Stars::where('user_id', $user->id)->get();

        $this->assertCount(2, $stars);
    }

    /** @test */
    public function user_can_be_starred_by_multiple_users()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $starredUser = $this->createParticipant();

        Stars::create([
            'user_id' => $user1->id,
            'starred_user_id' => $starredUser->id,
        ]);

        Stars::create([
            'user_id' => $user2->id,
            'starred_user_id' => $starredUser->id,
        ]);

        $stars = Stars::where('starred_user_id', $starredUser->id)->get();

        $this->assertCount(2, $stars);
    }
}
