<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use Tests\Traits\CreatesTestEvents;
use App\Models\{EventSignup, EventDetail};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventSignupModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestEvents;

    /** @test */
    public function it_belongs_to_event_details()
    {
        $event = $this->createEvent();

        $signup = EventSignup::factory()->create([
            'event_id' => $event->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $signup->eventDetails);
        $this->assertEquals($event->id, $signup->eventDetails->id);
    }

    /** @test */
    public function it_stores_signup_open_date()
    {
        $event = $this->createEvent();
        $signupOpen = now()->addDays(5);

        $signup = EventSignup::factory()->create([
            'event_id' => $event->id,
            'signup_open' => $signupOpen,
        ]);

        $this->assertEquals($signupOpen->format('Y-m-d H:i:s'), $signup->signup_open);
    }

    /** @test */
    public function it_stores_normal_signup_start_advanced_close()
    {
        $event = $this->createEvent();
        $normalSignup = now()->addDays(10);

        $signup = EventSignup::factory()->create([
            'event_id' => $event->id,
            'normal_signup_start_advanced_close' => $normalSignup,
        ]);

        $this->assertEquals($normalSignup->format('Y-m-d H:i:s'), $signup->normal_signup_start_advanced_close);
    }

    /** @test */
    public function it_stores_signup_close_date()
    {
        $event = $this->createEvent();
        $signupClose = now()->addDays(20);

        $signup = EventSignup::factory()->create([
            'event_id' => $event->id,
            'signup_close' => $signupClose,
        ]);

        $this->assertEquals($signupClose->format('Y-m-d H:i:s'), $signup->signup_close);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $signup = new EventSignup();
        $this->assertNull($signup->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $event = $this->createEvent();
        $signupOpen = now()->addDays(5);
        $normalSignup = now()->addDays(10);
        $signupClose = now()->addDays(20);

        $signup = EventSignup::factory()->create([
            'event_id' => $event->id,
            'signup_open' => $signupOpen,
            'normal_signup_start_advanced_close' => $normalSignup,
            'signup_close' => $signupClose,
        ]);

        $this->assertEquals($event->id, $signup->event_id);
        $this->assertEquals($signupOpen->format('Y-m-d H:i:s'), $signup->signup_open);
        $this->assertEquals($normalSignup->format('Y-m-d H:i:s'), $signup->normal_signup_start_advanced_close);
        $this->assertEquals($signupClose->format('Y-m-d H:i:s'), $signup->signup_close);
    }

    /** @test */
    public function it_can_update_signup_dates()
    {
        $event = $this->createEvent();
        $signup = EventSignup::factory()->create([
            'event_id' => $event->id,
            'signup_open' => now()->addDays(5),
        ]);

        $newDate = now()->addDays(7);
        $signup->update(['signup_open' => $newDate]);

        $this->assertEquals($newDate->format('Y-m-d H:i:s'), $signup->fresh()->signup_open);
    }
}
