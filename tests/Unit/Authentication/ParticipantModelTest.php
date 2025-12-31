<?php

namespace Tests\Unit\Authentication;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Participant, User, CountryRegion};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $this->assertInstanceOf(User::class, $user->participant->user);
        $this->assertEquals($user->id, $user->participant->user->id);
    }

    /** @test */
    public function it_stores_birthday()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;
        $participant->update(['birthday' => '1995-05-15']);

        $this->assertEquals('1995-05-15', $participant->fresh()->birthday);
    }

    /** @test */
    public function it_stores_nickname()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;
        $participant->update(['nickname' => 'ProGamer123']);

        $this->assertEquals('ProGamer123', $participant->fresh()->nickname);
    }

    /** @test */
    public function it_stores_bio()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;
        $participant->update(['bio' => 'Professional esports player']);

        $this->assertEquals('Professional esports player', $participant->fresh()->bio);
    }

    /** @test */
    public function it_stores_age()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;
        $participant->update(['age' => 25]);

        $this->assertEquals(25, $participant->fresh()->age);
    }

    /** @test */
    public function it_casts_games_data_to_array()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;
        $gamesData = ['game1' => 'CS2', 'game2' => 'Valorant'];

        $participant->update(['games_data' => $gamesData]);

        $fresh = $participant->fresh();
        $this->assertIsArray($fresh->games_data);
        $this->assertEquals($gamesData, $fresh->games_data);
    }

    /** @test */
    public function it_stores_region_information()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;

        $participant->update([
            'region' => 1,
            'region_name' => 'United States',
            'region_flag' => '🇺🇸',
        ]);

        $fresh = $participant->fresh();
        $this->assertEquals(1, $fresh->region);
        $this->assertEquals('United States', $fresh->region_name);
        $this->assertEquals('🇺🇸', $fresh->region_flag);
    }

    /** @test */
    public function it_stores_is_age_visible()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;
        $participant->update(['isAgeVisible' => true]);

        $this->assertTrue((bool) $participant->fresh()->isAgeVisible);
    }

    /** @test */
    public function it_stores_team_left_at()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;
        $leftAt = now();

        $participant->update(['team_left_at' => $leftAt]);

        $this->assertEquals($leftAt->format('Y-m-d H:i:s'), $participant->fresh()->team_left_at);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;

        $participant->update([
            'birthday' => '1998-03-20',
            'domain' => 'progamer',
            'age' => 26,
            'bio' => 'Competitive gamer',
            'nickname' => 'ShadowPlayer',
            'region' => 2,
            'isAgeVisible' => false,
        ]);

        $fresh = $participant->fresh();
        $this->assertEquals('1998-03-20', $fresh->birthday);
        $this->assertEquals('progamer', $fresh->domain);
        $this->assertEquals(26, $fresh->age);
        $this->assertEquals('Competitive gamer', $fresh->bio);
        $this->assertEquals('ShadowPlayer', $fresh->nickname);
        $this->assertEquals(2, $fresh->region);
        $this->assertFalse((bool) $fresh->isAgeVisible);
    }

    /** @test */
    public function it_can_have_empty_games_data()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;

        $participant->update(['games_data' => []]);

        $this->assertEmpty($participant->fresh()->games_data);
    }

    /** @test */
    public function it_can_have_null_bio()
    {
        $user = $this->createParticipant();
        $participant = $user->participant;

        $participant->update(['bio' => null]);

        $this->assertNull($participant->fresh()->bio);
    }
}
