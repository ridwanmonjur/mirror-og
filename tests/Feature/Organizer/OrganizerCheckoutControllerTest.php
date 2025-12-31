<?php

namespace Tests\Feature\Organizer;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestPayments};
use App\Models\{Coupon, SystemCoupon, UserCoupon};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganizerCheckoutControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestPayments;

    private $organizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organizer = $this->createOrganizer();
    }

    /** @test */
    public function organizer_can_view_checkout_page()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventEntry' => 100.00,
        ]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/checkout");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Checkout.Index');
        $response->assertViewHas('event');
    }

    /** @test */
    public function organizer_cannot_view_checkout_for_others_event()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/checkout");

        $response->assertForbidden();
    }

    /** @test */
    public function organizer_can_redeem_valid_coupon()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventEntry' => 100.00,
        ]);

        $systemCoupon = SystemCoupon::factory()->create([
            'discount_percentage' => 10,
            'is_active' => true,
        ]);

        $coupon = Coupon::factory()->create([
            'system_coupon_id' => $systemCoupon->id,
            'code' => 'DISCOUNT10',
            'is_used' => false,
        ]);

        $response = $this->actingAs($this->organizer)
            ->post("/organizer/events/{$event->id}/checkout", [
                'coupon_code' => 'DISCOUNT10',
            ]);

        $response->assertStatus(200);
        $response->assertViewHas('discountAmount', 10);
    }

    /** @test */
    public function organizer_cannot_redeem_invalid_coupon()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventEntry' => 100.00,
        ]);

        $response = $this->actingAs($this->organizer)
            ->post("/organizer/events/{$event->id}/checkout", [
                'coupon_code' => 'INVALID',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function organizer_cannot_redeem_already_used_coupon()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
        ]);

        $systemCoupon = SystemCoupon::factory()->create(['is_active' => true]);
        $coupon = Coupon::factory()->create([
            'system_coupon_id' => $systemCoupon->id,
            'code' => 'USED',
            'is_used' => true,
        ]);

        $response = $this->actingAs($this->organizer)
            ->post("/organizer/events/{$event->id}/checkout", [
                'coupon_code' => 'USED',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function organizer_can_view_checkout_transition()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
        ]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/checkout/transition");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Checkout.Transition');
    }

    /** @test */
    public function guest_cannot_access_checkout_pages()
    {
        $event = $this->createEvent();

        $this->get("/organizer/events/{$event->id}/checkout")
            ->assertRedirect('/login');

        $this->get("/organizer/events/{$event->id}/checkout/transition")
            ->assertRedirect('/login');
    }

    /** @test */
    public function participant_cannot_access_organizer_checkout()
    {
        $participant = $this->createParticipant();
        $event = $this->createEvent();

        $this->actingAs($participant)
            ->get("/organizer/events/{$event->id}/checkout")
            ->assertForbidden();
    }

    /** @test */
    public function checkout_calculates_discount_correctly()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventEntry' => 200.00,
        ]);

        $systemCoupon = SystemCoupon::factory()->create([
            'discount_percentage' => 25,
            'is_active' => true,
        ]);

        $coupon = Coupon::factory()->create([
            'system_coupon_id' => $systemCoupon->id,
            'code' => 'SAVE25',
            'is_used' => false,
        ]);

        $response = $this->actingAs($this->organizer)
            ->post("/organizer/events/{$event->id}/checkout", [
                'coupon_code' => 'SAVE25',
            ]);

        $response->assertStatus(200);
        $response->assertViewHas('discountAmount', 50); // 25% of 200
    }
}
