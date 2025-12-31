<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\CreatesTestPayments;
use App\Models\RecordStripe;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RecordStripeModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestPayments;

    /** @test */
    public function it_creates_stripe_payment_record()
    {
        $payment = $this->createStripePayment(100.00, 'succeeded');

        $this->assertDatabaseHas('stripe_transactions', [
            'id' => $payment->id,
            'payment_amount' => 100.00,
            'payment_status' => 'succeeded',
        ]);
    }

    /** @test */
    public function it_has_payment_id()
    {
        $payment = $this->createStripePayment(50.00, 'requires_capture', [
            'payment_id' => 'pi_unique_123',
        ]);

        $this->assertEquals('pi_unique_123', $payment->payment_id);
    }

    /** @test */
    public function it_tracks_payment_status()
    {
        $payment = $this->createStripePayment(100.00, 'requires_capture');

        $this->assertEquals('requires_capture', $payment->payment_status);

        $payment->update(['payment_status' => 'succeeded']);

        $this->assertEquals('succeeded', $payment->payment_status);
    }

    /** @test */
    public function it_can_be_refunded()
    {
        $payment = $this->createRefundedStripePayment(100.00);

        $this->assertEquals('refunded', $payment->payment_status);
    }

    /** @test */
    public function it_can_be_canceled()
    {
        $payment = $this->createCanceledStripePayment(50.00);

        $this->assertEquals('canceled', $payment->payment_status);
    }

    /** @test */
    public function it_can_be_released()
    {
        $payment = $this->createStripePayment(100.00, 'requires_capture');
        $payment->update(['payment_status' => 'released']);

        $this->assertEquals('released', $payment->payment_status);
    }

    /** @test */
    public function it_can_be_couponed()
    {
        $payment = $this->createStripePayment(100.00, 'succeeded');
        $payment->update(['payment_status' => 'couponed']);

        $this->assertEquals('couponed', $payment->payment_status);
    }

    /** @test */
    public function it_stores_payment_amount_as_decimal()
    {
        $payment = $this->createStripePayment(99.99);

        $this->assertEquals(99.99, $payment->payment_amount);
        $this->assertIsFloat($payment->payment_amount);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $payment = RecordStripe::factory()->create([
            'payment_id' => 'pi_test',
            'payment_amount' => 50.00,
            'payment_status' => 'succeeded',
        ]);

        $this->assertEquals('pi_test', $payment->payment_id);
        $this->assertEquals(50.00, $payment->payment_amount);
        $this->assertEquals('succeeded', $payment->payment_status);
    }

    /** @test */
    public function it_tracks_different_payment_statuses()
    {
        $statuses = [
            'requires_payment_method',
            'requires_capture',
            'succeeded',
            'canceled',
            'refunded',
            'released',
            'couponed'
        ];

        foreach ($statuses as $status) {
            $payment = $this->createStripePayment(100.00, $status);
            $this->assertEquals($status, $payment->payment_status);
        }
    }
}
