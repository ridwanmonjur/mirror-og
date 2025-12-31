<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{EventTier, User, EventTierSignup, EventTierPrize};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventTierModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $organizer = $this->createOrganizer();

        $tier = EventTier::factory()->create([
            'user_id' => $organizer->id,
        ]);

        $this->assertInstanceOf(User::class, $tier->user);
        $this->assertEquals($organizer->id, $tier->user->id);
    }

    /** @test */
    public function it_can_have_null_user()
    {
        $tier = EventTier::factory()->create([
            'user_id' => null,
        ]);

        $this->assertNull($tier->user_id);
    }

    /** @test */
    public function it_has_tier_signups_relationship()
    {
        $tier = EventTier::factory()->create();

        EventTierSignup::factory()->create(['tier_id' => $tier->id]);
        EventTierSignup::factory()->create(['tier_id' => $tier->id]);

        $tier->refresh();
        $this->assertCount(2, $tier->tierSignups);
        $this->assertInstanceOf(EventTierSignup::class, $tier->tierSignups->first());
    }

    /** @test */
    public function it_has_prizes_relationship()
    {
        $tier = EventTier::factory()->create();

        EventTierPrize::factory()->create(['event_tier_id' => $tier->id]);
        EventTierPrize::factory()->create(['event_tier_id' => $tier->id]);

        $tier->refresh();
        $this->assertCount(2, $tier->prizes);
        $this->assertInstanceOf(EventTierPrize::class, $tier->prizes->first());
    }

    /** @test */
    public function it_scopes_by_user_or_null_user()
    {
        $organizer = $this->createOrganizer();

        $userTier = EventTier::factory()->create(['user_id' => $organizer->id]);
        $globalTier = EventTier::factory()->create(['user_id' => null]);
        $otherUserTier = EventTier::factory()->create(['user_id' => $this->createOrganizer()->id]);

        $results = EventTier::byUserOrNullUser($organizer->id)->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($userTier));
        $this->assertTrue($results->contains($globalTier));
        $this->assertFalse($results->contains($otherUserTier));
    }

    /** @test */
    public function it_stores_tier_name()
    {
        $tier = EventTier::factory()->create([
            'eventTier' => 'Premium',
        ]);

        $this->assertEquals('Premium', $tier->eventTier);
    }

    /** @test */
    public function it_stores_tier_icon()
    {
        $tier = EventTier::factory()->create([
            'tierIcon' => 'premium-icon.png',
        ]);

        $this->assertEquals('premium-icon.png', $tier->tierIcon);
    }

    /** @test */
    public function it_stores_tier_team_slot()
    {
        $tier = EventTier::factory()->create([
            'tierTeamSlot' => 32,
        ]);

        $this->assertEquals(32, $tier->tierTeamSlot);
    }

    /** @test */
    public function it_stores_tier_prize_pool()
    {
        $tier = EventTier::factory()->create([
            'tierPrizePool' => 10000.00,
        ]);

        $this->assertEquals(10000.00, $tier->tierPrizePool);
    }

    /** @test */
    public function it_stores_tier_entry_fee()
    {
        $tier = EventTier::factory()->create([
            'tierEntryFee' => 50.00,
        ]);

        $this->assertEquals(50.00, $tier->tierEntryFee);
    }

    /** @test */
    public function it_stores_early_entry_fee()
    {
        $tier = EventTier::factory()->create([
            'earlyEntryFee' => 40.00,
        ]);

        $this->assertEquals(40.00, $tier->earlyEntryFee);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $tier = new EventTier();
        $this->assertFalse($tier->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $tier = EventTier::factory()->create([
            'eventTier' => 'Elite',
            'tierIcon' => 'elite.png',
            'tierTeamSlot' => 16,
            'tierPrizePool' => 5000.00,
            'tierEntryFee' => 25.00,
            'earlyEntryFee' => 20.00,
        ]);

        $this->assertEquals('Elite', $tier->eventTier);
        $this->assertEquals('elite.png', $tier->tierIcon);
        $this->assertEquals(16, $tier->tierTeamSlot);
        $this->assertEquals(5000.00, $tier->tierPrizePool);
        $this->assertEquals(25.00, $tier->tierEntryFee);
        $this->assertEquals(20.00, $tier->earlyEntryFee);
    }
}
