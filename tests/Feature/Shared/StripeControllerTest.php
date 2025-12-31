<?php

namespace Tests\Feature\Shared;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestPayments};
use Tests\Mocks\MocksStripe;
use App\Models\{RecordStripe, TransactionHistory, UserCoupon, Withdrawal};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StripeControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestPayments, MocksStripe;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
        $this->mockStripeClient();
    }

    /** @test */
    public function participant_can_view_wallet_dashboard()
    {
        $response = $this->actingAs($this->participant)
            ->get('/wallet');

        $response->assertStatus(200);
        $response->assertViewIs('Users.Dashboard');
        $response->assertViewHas('wallet');
    }

    /** @test */
    public function participant_can_view_transaction_history()
    {
        TransactionHistory::factory()->create([
            'user_id' => $this->participant->id,
            'amount' => 100.00,
            'type' => 'deposit',
        ]);

        $response = $this->actingAs($this->participant)
            ->get('/wallet/transactions');

        $response->assertStatus(200);
        $response->assertViewIs('Users.Transaction');
        $response->assertViewHas('transactions');
    }

    /** @test */
    public function participant_can_view_coupons()
    {
        $response = $this->actingAs($this->participant)
            ->get('/wallet/coupons');

        $response->assertStatus(200);
        $response->assertViewIs('Users.Coupon');
        $response->assertViewHas('coupons');
    }

    /** @test */
    public function participant_can_view_payment_method_form()
    {
        $response = $this->actingAs($this->participant)
            ->get('/wallet/payment-method');

        $response->assertStatus(200);
        $response->assertViewIs('Users.PaymentMethod');
    }

    /** @test */
    public function participant_can_save_payment_method()
    {
        $paymentData = [
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder_name' => 'Test User',
        ];

        $response = $this->actingAs($this->participant)
            ->post('/wallet/payment-method', $paymentData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_wallet', [
            'user_id' => $this->participant->id,
            'bank_name' => 'Test Bank',
            'has_bank_account' => true,
        ]);
    }


    /** @test */
    public function participant_can_checkout_wallet_topup()
    {
        $topupData = [
            'topup_amount' => 100.00,
        ];

        $response = $this->actingAs($this->participant)
            ->post('/wallet/checkout', $topupData);

        $response->assertStatus(200);
        $response->assertViewIs('Users.TopupStripe');
    }

    /** @test */
    public function guest_cannot_access_wallet_pages()
    {
        $this->get('/wallet')->assertRedirect(route('participant.signin.view'));
        $this->get('/wallet/transactions')->assertRedirect(route('participant.signin.view'));
        $this->get('/wallet/coupons')->assertRedirect(route('participant.signin.view'));
        $this->post('/wallet/checkout', [])->assertRedirect(route('participant.signin.view'));
    }

    /** @test */
    public function transaction_history_is_paginated()
    {
        TransactionHistory::factory()->count(15)->create([
            'user_id' => $this->participant->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->get('/wallet/transactions');

        $response->assertStatus(200);
        $response->assertViewHas('transactions');
    }

    /** @test */
    public function user_only_sees_own_transactions()
    {
        $otherUser = $this->createParticipant();

        TransactionHistory::factory()->create([
            'user_id' => $this->participant->id,
            'type' => 'deposit',
        ]);

        TransactionHistory::factory()->create([
            'user_id' => $otherUser->id,
            'type' => 'withdrawal',
        ]);

        $response = $this->actingAs($this->participant)
            ->get('/wallet/transactions');

        $transactions = $response->viewData('transactions');

        $this->assertIsArray($transactions);
        $this->assertCount(1, $transactions['data']);
    }
}
