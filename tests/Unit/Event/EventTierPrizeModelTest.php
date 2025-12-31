<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use App\Models\{EventTierPrize, EventTier};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventTierPrizeModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_event_tier()
    {
        $tier = EventTier::factory()->create();

        $prize = EventTierPrize::factory()->create([
            'event_tier_id' => $tier->id,
        ]);

        $this->assertInstanceOf(EventTier::class, $prize->eventTier);
        $this->assertEquals($tier->id, $prize->eventTier->id);
    }

    /** @test */
    public function it_stores_position()
    {
        $tier = EventTier::factory()->create();

        $prize = EventTierPrize::factory()->create([
            'event_tier_id' => $tier->id,
            'position' => 1,
        ]);

        $this->assertEquals(1, $prize->position);
    }

    /** @test */
    public function it_casts_position_to_integer()
    {
        $tier = EventTier::factory()->create();

        $prize = EventTierPrize::factory()->create([
            'event_tier_id' => $tier->id,
            'position' => '2',
        ]);

        $this->assertIsInt($prize->position);
        $this->assertEquals(2, $prize->position);
    }

    /** @test */
    public function it_stores_prize_sum()
    {
        $tier = EventTier::factory()->create();

        $prize = EventTierPrize::factory()->create([
            'event_tier_id' => $tier->id,
            'prize_sum' => 1000.50,
        ]);

        $this->assertEquals('1000.50', $prize->prize_sum);
    }

    /** @test */
    public function it_casts_prize_sum_to_decimal()
    {
        $tier = EventTier::factory()->create();

        $prize = EventTierPrize::factory()->create([
            'event_tier_id' => $tier->id,
            'prize_sum' => 500,
        ]);

        $this->assertIsString($prize->prize_sum);
        $this->assertEquals('500.00', $prize->prize_sum);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $prize = new EventTierPrize();
        $this->assertFalse($prize->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $tier = EventTier::factory()->create();

        $prize = EventTierPrize::factory()->create([
            'event_tier_id' => $tier->id,
            'position' => 3,
            'prize_sum' => 250.75,
        ]);

        $this->assertEquals($tier->id, $prize->event_tier_id);
        $this->assertEquals(3, $prize->position);
        $this->assertEquals('250.75', $prize->prize_sum);
    }
}
