<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Mocks\MocksStripe;
use Tests\Traits\{CreatesTestUsers, CreatesTestPayments, CreatesTestTeams, CreatesTestEvents};
use App\Services\PaymentService;
use App\Models\{JoinEvent, RecordStripe, ParticipantPayment, Wallet};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentServiceTest extends TestCase
{
    use DatabaseTransactions, MocksStripe;
    use CreatesTestUsers, CreatesTestPayments, CreatesTestTeams, CreatesTestEvents;

    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        $stripeMock = $this->mockStripeClient();
        $this->paymentService = new PaymentService($stripeMock);
    }

    /** @test */
    public function it_refunds_with_zero_percent_capture_cancels_payment()
    {
        // Arrange
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $payment = $this->createStripePayment(100.00, 'requires_capture', [
            'payment_id' => 'pi_test123',
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);

        $paymentIntent = $this->mockPaymentIntentRetrieval('pi_test123', 'requires_capture', 10000);
        $paymentIntent->shouldReceive('cancel')->once()->andReturnSelf();

        // Act
        $result = $this->paymentService->refundPaymentsForEvents($joinEvent->id, 0);

        // Assert
        $this->assertArrayHasKey($user->id, $result);
        $this->assertEquals(100.00, $result[$user->id]['released_amount']);

        $payment->refresh();
        $this->assertEquals('released', $payment->payment_status);

        $joinEvent->refresh();
        $this->assertEquals('canceled', $joinEvent->join_status);
    }

    /** @test */
    public function it_refunds_with_fifty_percent_capture()
    {
        // Arrange
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $payment = $this->createStripePayment(100.00, 'requires_capture', [
            'payment_id' => 'pi_test456',
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);

        $paymentIntent = $this->mockPaymentIntentRetrieval('pi_test456', 'requires_capture', 10000);
        $paymentIntent->shouldReceive('capture')
            ->with(['amount_to_capture' => 5000]) // 50% of 100.00
            ->once()
            ->andReturnSelf();

        // Act
        $result = $this->paymentService->refundPaymentsForEvents($joinEvent->id, 0.5);

        // Assert
        $this->assertArrayHasKey($user->id, $result);
        $this->assertEquals(50.00, $result[$user->id]['released_amount']);

        $payment->refresh();
        $this->assertEquals('released', $payment->payment_status);
    }

    /** @test */
    public function it_adds_coupon_balance_for_succeeded_payments()
    {
        // Arrange
        $user = $this->createParticipant();
        $wallet = $this->createWallet($user, 0);
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $payment = $this->createStripePayment(100.00, 'succeeded', [
            'payment_id' => 'pi_succeeded',
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);

        // Act
        $result = $this->paymentService->refundPaymentsForEvents($joinEvent->id, 0.5);

        // Assert
        $this->assertArrayHasKey($user->id, $result);
        $this->assertEquals(50.00, $result[$user->id]['couponed_amount']);

        $wallet->refresh();
        $this->assertEquals(50.00, $wallet->usable_balance);

        $payment->refresh();
        $this->assertEquals('couponed', $payment->payment_status);
    }

    /** @test */
    public function it_handles_multiple_payments_for_same_join_event()
    {
        // Arrange
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeamWithMembers(1, $user1);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        // Two payments - one requires_capture, one succeeded
        $payment1 = $this->createStripePayment(100.00, 'requires_capture', [
            'payment_id' => 'pi_multi1',
        ]);

        $payment2 = $this->createStripePayment(80.00, 'succeeded', [
            'payment_id' => 'pi_multi2',
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment1->id,
            'user_id' => $user1->id,
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment2->id,
            'user_id' => $user2->id,
        ]);

        $this->createWallet($user1, 0);
        $this->createWallet($user2, 0);

        $paymentIntent1 = $this->mockPaymentIntentRetrieval('pi_multi1', 'requires_capture', 10000);
        $paymentIntent1->shouldReceive('cancel')->once()->andReturnSelf();

        // Act
        $result = $this->paymentService->refundPaymentsForEvents($joinEvent->id, 0);

        // Assert
        $this->assertArrayHasKey($user1->id, $result);
        $this->assertArrayHasKey($user2->id, $result);

        $this->assertEquals(100.00, $result[$user1->id]['released_amount']);
        $this->assertEquals(80.00, $result[$user2->id]['couponed_amount']);

        $payment1->refresh();
        $this->assertEquals('released', $payment1->payment_status);

        $payment2->refresh();
        $this->assertEquals('couponed', $payment2->payment_status);

        $user2->wallet->refresh();
        $this->assertEquals(80.00, $user2->wallet->usable_balance);
    }

    /** @test */
    public function it_handles_full_capture_hundred_percent()
    {
        // Arrange
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $payment = $this->createStripePayment(100.00, 'requires_capture', [
            'payment_id' => 'pi_full',
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);

        $paymentIntent = $this->mockPaymentIntentRetrieval('pi_full', 'requires_capture', 10000);
        $paymentIntent->shouldReceive('capture')
            ->with(['amount_to_capture' => 10000]) // 100% of 100.00
            ->once()
            ->andReturnSelf();

        // Act
        $result = $this->paymentService->refundPaymentsForEvents($joinEvent->id, 1.0);

        // Assert
        $this->assertArrayHasKey($user->id, $result);
        $this->assertEquals(0.00, $result[$user->id]['released_amount']);

        $payment->refresh();
        $this->assertEquals('released', $payment->payment_status);
    }

    /** @test */
    public function it_skips_payment_if_status_changed_before_processing()
    {
        // Arrange
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
        ]);

        $payment = $this->createStripePayment(100.00, 'requires_capture', [
            'payment_id' => 'pi_changed',
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);

        // Mock payment intent that's already captured
        $paymentIntent = $this->mockPaymentIntentRetrieval('pi_changed', 'succeeded', 10000);
        $paymentIntent->shouldNotReceive('cancel');
        $paymentIntent->shouldNotReceive('capture');

        // Act
        $result = $this->paymentService->refundPaymentsForEvents($joinEvent->id, 0);

        // Assert
        // Should continue without error, but payment not in result
        $this->assertIsArray($result);
    }

    /** @test */
    public function it_updates_join_event_status_to_canceled()
    {
        // Arrange
        $user = $this->createParticipant();
        $event = $this->createEvent();
        $team = $this->createTeam([], $user);

        $joinEvent = JoinEvent::factory()->create([
            'event_details_id' => $event->id,
            'team_id' => $team->id,
            'join_status' => 'confirmed',
        ]);

        $payment = $this->createStripePayment(50.00, 'succeeded', [
            'payment_id' => 'pi_status',
        ]);

        ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);

        $this->createWallet($user, 0);

        // Act
        $this->paymentService->refundPaymentsForEvents($joinEvent->id, 0);

        // Assert
        $joinEvent->refresh();
        $this->assertEquals('canceled', $joinEvent->join_status);
    }

    protected function tearDown(): void
    {
        $this->tearDownMockery();
        parent::tearDown();
    }
}
