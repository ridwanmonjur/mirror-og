<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{OrganizerFollow, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganizerFollowModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_participant_user()
    {
        $participant = $this->createParticipant();
        $organizer = $this->createOrganizer();

        $follow = OrganizerFollow::factory()->create([
            'participant_user_id' => $participant->id,
            'organizer_user_id' => $organizer->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->participantUser);
        $this->assertEquals($participant->id, $follow->participantUser->id);
    }

    /** @test */
    public function it_belongs_to_organizer()
    {
        $participant = $this->createParticipant();
        $organizer = $this->createOrganizer();

        $follow = OrganizerFollow::factory()->create([
            'participant_user_id' => $participant->id,
            'organizer_user_id' => $organizer->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->organizer);
        $this->assertEquals($organizer->id, $follow->organizer->id);
    }

    /** @test */
    public function it_gets_followers_count()
    {
        $organizer = $this->createOrganizer();
        $participant1 = $this->createParticipant();
        $participant2 = $this->createParticipant();

        OrganizerFollow::factory()->create([
            'participant_user_id' => $participant1->id,
            'organizer_user_id' => $organizer->id,
        ]);

        OrganizerFollow::factory()->create([
            'participant_user_id' => $participant2->id,
            'organizer_user_id' => $organizer->id,
        ]);

        $count = OrganizerFollow::getFollowersCount($organizer->id);

        $this->assertEquals(2, $count);
    }

    /** @test */
    public function it_checks_if_following()
    {
        $participant = $this->createParticipant();
        $organizer = $this->createOrganizer();

        OrganizerFollow::factory()->create([
            'participant_user_id' => $participant->id,
            'organizer_user_id' => $organizer->id,
        ]);

        $isFollowing = OrganizerFollow::isFollowing($participant->id, $organizer->id);

        $this->assertTrue($isFollowing);
    }

    /** @test */
    public function it_returns_false_when_not_following()
    {
        $participant = $this->createParticipant();
        $organizer = $this->createOrganizer();

        $isFollowing = OrganizerFollow::isFollowing($participant->id, $organizer->id);

        $this->assertFalse($isFollowing);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $participant = $this->createParticipant();
        $organizer = $this->createOrganizer();

        $follow = OrganizerFollow::factory()->create([
            'participant_user_id' => $participant->id,
            'organizer_user_id' => $organizer->id,
        ]);

        $this->assertEquals($participant->id, $follow->participant_user_id);
        $this->assertEquals($organizer->id, $follow->organizer_user_id);
    }

    /** @test */
    public function multiple_participants_can_follow_same_organizer()
    {
        $organizer = $this->createOrganizer();
        $participant1 = $this->createParticipant();
        $participant2 = $this->createParticipant();
        $participant3 = $this->createParticipant();

        OrganizerFollow::factory()->create([
            'participant_user_id' => $participant1->id,
            'organizer_user_id' => $organizer->id,
        ]);

        OrganizerFollow::factory()->create([
            'participant_user_id' => $participant2->id,
            'organizer_user_id' => $organizer->id,
        ]);

        OrganizerFollow::factory()->create([
            'participant_user_id' => $participant3->id,
            'organizer_user_id' => $organizer->id,
        ]);

        $count = OrganizerFollow::getFollowersCount($organizer->id);

        $this->assertEquals(3, $count);
    }
}
