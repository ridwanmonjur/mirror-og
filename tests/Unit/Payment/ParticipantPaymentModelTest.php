<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestTeams, CreatesTestEvents, CreatesTestPayments};
use App\Models\{ParticipantPayment, User, TeamMember, RecordStripe, TransactionHistory, JoinEvent};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantPaymentModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestTeams, CreatesTestEvents, CreatesTestPayments;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $payment = ParticipantPayment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $payment->user);
        $this->assertEquals($user->id, $payment->user->id);
    }

    /** @test */
    public function it_belongs_to_team_member()
    {
        $team = $this->createTeam();
        $user = $this->createParticipant();
        $member = TeamMember::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $payment = ParticipantPayment::factory()->create([
            'team_members_id' => $member->id,
        ]);

        $this->assertInstanceOf(TeamMember::class, $payment->members);
        $this->assertEquals($member->id, $payment->members->id);
    }

    /** @test */
    public function it_belongs_to_stripe_transaction()
    {
        $stripePayment = $this->createStripePayment(100.00);

        $payment = ParticipantPayment::factory()->create([
            'payment_id' => $stripePayment->id,
        ]);

        $this->assertInstanceOf(RecordStripe::class, $payment->transaction);
        $this->assertEquals($stripePayment->id, $payment->transaction->id);
    }

    /** @test */
    public function it_belongs_to_transaction_history()
    {
        $user = $this->createParticipant();
        $history = TransactionHistory::factory()->create([
            'user_id' => $user->id,
        ]);

        $payment = ParticipantPayment::factory()->create([
            'history_id' => $history->id,
        ]);

        $this->assertInstanceOf(TransactionHistory::class, $payment->history);
        $this->assertEquals($history->id, $payment->history->id);
    }

    /** @test */
    public function it_stores_payment_amount()
    {
        $payment = ParticipantPayment::factory()->create([
            'payment_amount' => 50.00,
        ]);

        $this->assertEquals(50.00, $payment->payment_amount);
    }

    /** @test */
    public function it_stores_payment_id()
    {
        $stripePayment = $this->createStripePayment(100.00);

        $payment = ParticipantPayment::factory()->create([
            'payment_id' => $stripePayment->id,
        ]);

        $this->assertEquals($stripePayment->id, $payment->payment_id);
    }

    /** @test */
    public function it_stores_register_time()
    {
        $payment = ParticipantPayment::factory()->create([
            'register_time' => 'early',
        ]);

        $this->assertEquals('early', $payment->register_time);
    }

    /** @test */
    public function it_stores_payment_type()
    {
        $payment = ParticipantPayment::factory()->create([
            'type' => 'entry_fee',
        ]);

        $this->assertEquals('entry_fee', $payment->type);
    }

    /** @test */
    public function it_links_to_join_event()
    {
        $event = $this->createEvent();
        $team = $this->createTeam();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $payment = ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
        ]);

        $this->assertEquals($joinEvent->id, $payment->join_events_id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();
        $team = $this->createTeam();
        $member = TeamMember::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
        $event = $this->createEvent();
        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);
        $stripePayment = $this->createStripePayment(75.00);

        $payment = ParticipantPayment::factory()->create([
            'team_members_id' => $member->id,
            'user_id' => $user->id,
            'join_events_id' => $joinEvent->id,
            'payment_amount' => 75.00,
            'payment_id' => $stripePayment->id,
            'register_time' => 'normal',
            'type' => 'tournament_entry',
        ]);

        $this->assertEquals($member->id, $payment->team_members_id);
        $this->assertEquals($user->id, $payment->user_id);
        $this->assertEquals($joinEvent->id, $payment->join_events_id);
        $this->assertEquals(75.00, $payment->payment_amount);
        $this->assertEquals($stripePayment->id, $payment->payment_id);
        $this->assertEquals('normal', $payment->register_time);
        $this->assertEquals('tournament_entry', $payment->type);
    }

    /** @test */
    public function it_tracks_different_payment_types()
    {
        $types = ['entry_fee', 'additional_fee', 'refund', 'withdrawal'];

        foreach ($types as $type) {
            $payment = ParticipantPayment::factory()->create([
                'type' => $type,
            ]);
            $this->assertEquals($type, $payment->type);
        }
    }
}
