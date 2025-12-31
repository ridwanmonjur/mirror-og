<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{PaymentIntent, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentIntentModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $paymentIntent = PaymentIntent::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $paymentIntent->user);
        $this->assertEquals($user->id, $paymentIntent->user->id);
    }

    /** @test */
    public function it_stores_payment_intent_id()
    {
        $user = $this->createParticipant();

        $paymentIntent = PaymentIntent::factory()->create([
            'user_id' => $user->id,
            'payment_intent_id' => 'pi_test_123456',
        ]);

        $this->assertEquals('pi_test_123456', $paymentIntent->payment_intent_id);
    }

    /** @test */
    public function it_stores_customer_id()
    {
        $user = $this->createParticipant();

        $paymentIntent = PaymentIntent::factory()->create([
            'user_id' => $user->id,
            'customer_id' => 'cus_test_123456',
        ]);

        $this->assertEquals('cus_test_123456', $paymentIntent->customer_id);
    }

    /** @test */
    public function it_stores_payment_status()
    {
        $user = $this->createParticipant();

        $paymentIntent = PaymentIntent::factory()->create([
            'user_id' => $user->id,
            'status' => 'requires_capture',
        ]);

        $this->assertEquals('requires_capture', $paymentIntent->status);
    }

    /** @test */
    public function it_stores_amount()
    {
        $user = $this->createParticipant();

        $paymentIntent = PaymentIntent::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000, // Amount in cents
        ]);

        $this->assertEquals(10000, $paymentIntent->amount);
    }

    /** @test */
    public function it_tracks_different_payment_statuses()
    {
        $user = $this->createParticipant();
        $statuses = [
            'requires_payment_method',
            'requires_confirmation',
            'requires_action',
            'requires_capture',
            'processing',
            'succeeded',
            'canceled',
        ];

        foreach ($statuses as $status) {
            $paymentIntent = PaymentIntent::factory()->create([
                'user_id' => $user->id,
                'status' => $status,
            ]);
            $this->assertEquals($status, $paymentIntent->status);
        }
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $paymentIntent = PaymentIntent::factory()->create([
            'user_id' => $user->id,
            'payment_intent_id' => 'pi_test',
            'customer_id' => 'cus_test',
            'status' => 'succeeded',
            'amount' => 5000,
        ]);

        $this->assertEquals($user->id, $paymentIntent->user_id);
        $this->assertEquals('pi_test', $paymentIntent->payment_intent_id);
        $this->assertEquals('cus_test', $paymentIntent->customer_id);
        $this->assertEquals('succeeded', $paymentIntent->status);
        $this->assertEquals(5000, $paymentIntent->amount);
    }

    /** @test */
    public function it_can_update_status()
    {
        $user = $this->createParticipant();

        $paymentIntent = PaymentIntent::factory()->create([
            'user_id' => $user->id,
            'status' => 'requires_capture',
        ]);

        $paymentIntent->update(['status' => 'succeeded']);

        $this->assertEquals('succeeded', $paymentIntent->fresh()->status);
    }
}
