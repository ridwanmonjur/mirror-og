<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use App\Models\{EventTierSignup, EventTier, EventType};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventTierSignupModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_tier()
    {
        $tier = EventTier::factory()->create();

        $signup = EventTierSignup::factory()->create([
            'tier_id' => $tier->id,
        ]);

        $this->assertInstanceOf(EventTier::class, $signup->tier);
        $this->assertEquals($tier->id, $signup->tier->id);
    }

    /** @test */
    public function it_belongs_to_type()
    {
        $type = EventType::factory()->create();

        $signup = EventTierSignup::factory()->create([
            'type_id' => $type->id,
        ]);

        $this->assertInstanceOf(EventType::class, $signup->type);
        $this->assertEquals($type->id, $signup->type->id);
    }

    /** @test */
    public function it_stores_signup_open_date()
    {
        $signupOpen = now()->addDays(5);

        $signup = EventTierSignup::factory()->create([
            'signup_open' => $signupOpen,
        ]);

        $this->assertEquals($signupOpen->format('Y-m-d H:i:s'), $signup->signup_open);
    }

    /** @test */
    public function it_stores_signup_close_date()
    {
        $signupClose = now()->addDays(15);

        $signup = EventTierSignup::factory()->create([
            'signup_close' => $signupClose,
        ]);

        $this->assertEquals($signupClose->format('Y-m-d H:i:s'), $signup->signup_close);
    }

    /** @test */
    public function it_stores_normal_signup_start_advanced_close()
    {
        $normalSignup = now()->addDays(10);

        $signup = EventTierSignup::factory()->create([
            'normal_signup_start_advanced_close' => $normalSignup,
        ]);

        $this->assertEquals($normalSignup->format('Y-m-d H:i:s'), $signup->normal_signup_start_advanced_close);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $signup = new EventTierSignup();
        $this->assertNull($signup->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $tier = EventTier::factory()->create();
        $type = EventType::factory()->create();
        $signupOpen = now()->addDays(3);
        $normalSignup = now()->addDays(8);
        $signupClose = now()->addDays(12);

        $signup = EventTierSignup::factory()->create([
            'tier_id' => $tier->id,
            'type_id' => $type->id,
            'signup_open' => $signupOpen,
            'signup_close' => $signupClose,
            'normal_signup_start_advanced_close' => $normalSignup,
        ]);

        $this->assertEquals($tier->id, $signup->tier_id);
        $this->assertEquals($type->id, $signup->type_id);
        $this->assertEquals($signupOpen->format('Y-m-d H:i:s'), $signup->signup_open);
        $this->assertEquals($signupClose->format('Y-m-d H:i:s'), $signup->signup_close);
        $this->assertEquals($normalSignup->format('Y-m-d H:i:s'), $signup->normal_signup_start_advanced_close);
    }

    /** @test */
    public function it_can_update_signup_dates()
    {
        $signup = EventTierSignup::factory()->create([
            'signup_open' => now()->addDays(5),
        ]);

        $newDate = now()->addDays(7);
        $signup->update(['signup_open' => $newDate]);

        $this->assertEquals($newDate->format('Y-m-d H:i:s'), $signup->fresh()->signup_open);
    }
}
