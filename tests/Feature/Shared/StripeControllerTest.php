<?php

namespace Tests\Feature\Shared;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestPayments, MocksStripe};
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
        $response->assertViewIs('Shared.Wallet.Dashboard');
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
        $response->assertViewIs('Shared.Wallet.Transactions');
        $response->assertViewHas('transactions');
    }

    /** @test */
    public function participant_can_view_coupons()
    {
        UserCoupon::factory()->create([
            'user_id' => $this->participant->id,
            'balance' => 50.00,
        ]);

        $response = $this->actingAs($this->participant)
            ->get('/wallet/coupons');

        $response->assertStatus(200);
        $response->assertViewIs('Shared.Wallet.Coupons');
        $response->assertViewHas('coupons');
    }

    /** @test */
    public function participant_can_view_payment_method_form()
    {
        $response = $this->actingAs($this->participant)
            ->get('/wallet/payment-methods');

        $response->assertStatus(200);
        $response->assertViewIs('Shared.Wallet.PaymentMethods');
    }

    /** @test */
    public function participant_can_save_payment_method()
    {
        $paymentData = [
            'payment_method_id' => 'pm_test_123',
        ];

        $response = $this->actingAs($this->participant)
            ->post('/wallet/payment-methods', $paymentData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('record_stripes', [
            'user_id' => $this->participant->id,
        ]);
    }

    /** @test */
    public function participant_can_request_withdrawal()
    {
        $participant = $this->createParticipantWithBalance(500.00);

        $withdrawalData = [
            'amount' => 100.00,
        ];

        $response = $this->actingAs($participant)
            ->post('/wallet/withdrawal', $withdrawalData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $participant->id,
            'withdrawal' => 100.00,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function participant_cannot_withdraw_more_than_daily_limit()
    {
        $participant = $this->createParticipantWithBalance(10000.00);

        $withdrawalData = [
            'amount' => 6000.00, // Exceeds MAX_DAILY_AMOUNT of 5000
        ];

        $response = $this->actingAs($participant)
            ->post('/wallet/withdrawal', $withdrawalData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function participant_cannot_withdraw_below_minimum()
    {
        $participant = $this->createParticipantWithBalance(100.00);

        $withdrawalData = [
            'amount' => 3.00, // Below MIN_AMOUNT of 5.00
        ];

        $response = $this->actingAs($participant)
            ->post('/wallet/withdrawal', $withdrawalData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function participant_cannot_withdraw_more_than_balance()
    {
        $participant = $this->createParticipantWithBalance(50.00);

        $withdrawalData = [
            'amount' => 100.00,
        ];

        $response = $this->actingAs($participant)
            ->post('/wallet/withdrawal', $withdrawalData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function participant_can_checkout_wallet_topup()
    {
        $topupData = [
            'amount' => 100.00,
        ];

        $response = $this->actingAs($this->participant)
            ->post('/wallet/topup/checkout', $topupData);

        $response->assertStatus(200);
        $response->assertViewIs('Shared.Wallet.TopupCheckout');
    }

    /** @test */
    public function guest_cannot_access_wallet_pages()
    {
        $this->get('/wallet')->assertRedirect('/login');
        $this->get('/wallet/transactions')->assertRedirect('/login');
        $this->get('/wallet/coupons')->assertRedirect('/login');
        $this->post('/wallet/withdrawal', [])->assertRedirect('/login');
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

        $this->assertEquals(1, $transactions->total());
    }
}
