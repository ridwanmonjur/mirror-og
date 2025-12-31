<?php

namespace Tests\Feature\Participant;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, CreatesTestTeams, CreatesTestPayments};
use App\Models\{JoinEvent, Coupon, SystemCoupon};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantCheckoutControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, CreatesTestTeams, CreatesTestPayments;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
    }

    /** @test */
    public function participant_can_view_checkout_page()
    {
        $event = $this->createEvent(['eventEntry' => 50.00]);
        $team = $this->createTeam([], $this->participant);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->get("/participant/checkout?join_events_id={$joinEvent->id}");

        $response->assertStatus(200);
        $response->assertViewIs('Participant.Checkout.Index');
    }

    /** @test */
    public function participant_can_checkout_with_wallet()
    {
        $participant = $this->createParticipantWithBalance(100.00);
        $event = $this->createEvent(['eventEntry' => 50.00]);
        $team = $this->createTeam([], $participant);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $participant->id,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($participant)
            ->post('/participant/checkout/wallet', [
                'join_events_id' => $joinEvent->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $joinEvent->refresh();
        $this->assertEquals('completed', $joinEvent->payment_status);

        $participant->wallet->refresh();
        $this->assertEquals(50.00, $participant->wallet->usable_balance);
    }

    /** @test */
    public function participant_cannot_checkout_with_insufficient_wallet_balance()
    {
        $participant = $this->createParticipantWithBalance(30.00);
        $event = $this->createEvent(['eventEntry' => 50.00]);
        $team = $this->createTeam([], $participant);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $participant->id,
        ]);

        $response = $this->actingAs($participant)
            ->post('/participant/checkout/wallet', [
                'join_events_id' => $joinEvent->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $joinEvent->refresh();
        $this->assertEquals('pending', $joinEvent->payment_status);
    }

    /** @test */
    public function participant_can_apply_coupon_at_checkout()
    {
        $event = $this->createEvent(['eventEntry' => 100.00]);
        $team = $this->createTeam([], $this->participant);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $this->participant->id,
        ]);

        $systemCoupon = SystemCoupon::factory()->create([
            'discount_percentage' => 20,
            'is_active' => true,
        ]);

        $coupon = Coupon::factory()->create([
            'system_coupon_id' => $systemCoupon->id,
            'code' => 'SAVE20',
            'is_used' => false,
        ]);

        $response = $this->actingAs($this->participant)
            ->get("/participant/checkout?join_events_id={$joinEvent->id}&coupon_code=SAVE20");

        $response->assertStatus(200);
        $response->assertViewHas('discountAmount', 20);
    }

    /** @test */
    public function participant_can_view_checkout_transition_page()
    {
        $response = $this->actingAs($this->participant)
            ->get('/participant/checkout/transition');

        $response->assertStatus(200);
        $response->assertViewIs('Participant.Checkout.Transition');
    }

    /** @test */
    public function guest_cannot_access_checkout()
    {
        $this->get('/participant/checkout')
            ->assertRedirect('/login');

        $this->post('/participant/checkout/wallet', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function organizer_cannot_access_participant_checkout()
    {
        $organizer = $this->createOrganizer();

        $this->actingAs($organizer)
            ->get('/participant/checkout')
            ->assertForbidden();
    }

    /** @test */
    public function it_validates_join_event_id_for_wallet_checkout()
    {
        $response = $this->actingAs($this->participant)
            ->post('/participant/checkout/wallet', []);

        $response->assertSessionHasErrors('join_events_id');
    }

    /** @test */
    public function wallet_checkout_deducts_correct_amount()
    {
        $participant = $this->createParticipantWithBalance(200.00);
        $event = $this->createEvent(['eventEntry' => 75.50]);
        $team = $this->createTeam([], $participant);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'joiner_id' => $participant->id,
        ]);

        $this->actingAs($participant)
            ->post('/participant/checkout/wallet', [
                'join_events_id' => $joinEvent->id,
            ]);

        $participant->wallet->refresh();
        $this->assertEquals(124.50, $participant->wallet->usable_balance);
        $this->assertEquals(124.50, $participant->wallet->total_balance);
    }
}
