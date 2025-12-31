<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{OrganizerPayment, User, TransactionHistory};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganizerPaymentModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $organizer = $this->createOrganizer();

        $payment = OrganizerPayment::factory()->create([
            'user_id' => $organizer->id,
        ]);

        $this->assertInstanceOf(User::class, $payment->user);
        $this->assertEquals($organizer->id, $payment->user->id);
    }

    /** @test */
    public function it_belongs_to_transaction_history()
    {
        $organizer = $this->createOrganizer();
        $history = TransactionHistory::factory()->create([
            'user_id' => $organizer->id,
        ]);

        $payment = OrganizerPayment::factory()->create([
            'user_id' => $organizer->id,
            'history_id' => $history->id,
        ]);

        $this->assertInstanceOf(TransactionHistory::class, $payment->history);
        $this->assertEquals($history->id, $payment->history->id);
    }

    /** @test */
    public function it_stores_payment_amount()
    {
        $payment = OrganizerPayment::factory()->create([
            'payment_amount' => 500.00,
        ]);

        $this->assertEquals(500.00, $payment->payment_amount);
    }

    /** @test */
    public function it_stores_discount_amount()
    {
        $payment = OrganizerPayment::factory()->create([
            'discount_amount' => 50.00,
        ]);

        $this->assertEquals(50.00, $payment->discount_amount);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $payment = new OrganizerPayment();
        $this->assertFalse($payment->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $organizer = $this->createOrganizer();

        $payment = OrganizerPayment::factory()->create([
            'payment_amount' => 1000.00,
            'discount_amount' => 100.00,
            'user_id' => $organizer->id,
            'payment_id' => 123,
        ]);

        $this->assertEquals(1000.00, $payment->payment_amount);
        $this->assertEquals(100.00, $payment->discount_amount);
        $this->assertEquals($organizer->id, $payment->user_id);
        $this->assertEquals(123, $payment->payment_id);
    }
}
