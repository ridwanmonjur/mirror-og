<?php

namespace Tests\Traits;

use App\Models\RecordStripe;
use App\Models\ParticipantPayment;
use App\Models\OrganizerPayment;
use App\Models\Wallet;
use App\Models\PaymentIntent;
use App\Models\TransactionHistory;

trait CreatesTestPayments
{
    /**
     * Create a wallet for user
     */
    protected function createWallet($user, $usableBalance = 100.00, $totalBalance = null)
    {
        $totalBalance = $totalBalance ?? $usableBalance;

        return Wallet::factory()->create([
            'user_id' => $user->id,
            'usable_balance' => $usableBalance,
            'total_balance' => $totalBalance,
        ]);
    }

    /**
     * Create a Stripe payment record
     */
    protected function createStripePayment($amount = 50.00, $status = 'succeeded', array $attributes = [])
    {
        return RecordStripe::factory()->create(array_merge([
            'payment_amount' => $amount,
            'payment_status' => $status,
            'payment_id' => 'pi_test_' . uniqid(),
        ], $attributes));
    }

    /**
     * Create a participant payment
     */
    protected function createParticipantPayment($joinEvent, $user, $payment = null)
    {
        if (!$payment) {
            $payment = $this->createStripePayment();
        }

        return ParticipantPayment::factory()->create([
            'join_events_id' => $joinEvent->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create an organizer payment
     */
    protected function createOrganizerPayment($event = null, $user = null, $amount = 100.00)
    {
        return OrganizerPayment::factory()->create([
            'event_details_id' => $event?->id,
            'user_id' => $user?->id,
            'amount' => $amount,
            'payment_status' => 'succeeded',
        ]);
    }

    /**
     * Create a payment intent
     */
    protected function createPaymentIntent($user, $amount = 50.00, $status = 'requires_payment_method')
    {
        return PaymentIntent::factory()->create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_intent_id' => 'pi_test_' . uniqid(),
            'status' => $status,
        ]);
    }

    /**
     * Create a transaction history record
     */
    protected function createTransaction($wallet, $amount, $type = 'credit', $description = 'Test transaction')
    {
        return TransactionHistory::factory()->create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
        ]);
    }

    /**
     * Create a pending Stripe payment (requires_capture)
     */
    protected function createPendingStripePayment($amount = 50.00, array $attributes = [])
    {
        return $this->createStripePayment($amount, 'requires_capture', $attributes);
    }

    /**
     * Create a succeeded Stripe payment
     */
    protected function createSucceededStripePayment($amount = 50.00, array $attributes = [])
    {
        return $this->createStripePayment($amount, 'succeeded', $attributes);
    }

    /**
     * Create a refunded Stripe payment
     */
    protected function createRefundedStripePayment($amount = 50.00, array $attributes = [])
    {
        return $this->createStripePayment($amount, 'refunded', $attributes);
    }

    /**
     * Create a canceled Stripe payment
     */
    protected function createCanceledStripePayment($amount = 50.00, array $attributes = [])
    {
        return $this->createStripePayment($amount, 'canceled', $attributes);
    }

    /**
     * Update wallet balance
     */
    protected function updateWalletBalance(Wallet $wallet, $usableBalance, $totalBalance = null)
    {
        $wallet->update([
            'usable_balance' => $usableBalance,
            'total_balance' => $totalBalance ?? $usableBalance,
        ]);

        return $wallet->fresh();
    }

    /**
     * Add balance to wallet
     */
    protected function addToWallet(Wallet $wallet, $amount)
    {
        $wallet->increment('usable_balance', $amount);
        $wallet->increment('total_balance', $amount);

        return $wallet->fresh();
    }

    /**
     * Deduct balance from wallet
     */
    protected function deductFromWallet(Wallet $wallet, $amount)
    {
        $wallet->decrement('usable_balance', $amount);
        $wallet->decrement('total_balance', $amount);

        return $wallet->fresh();
    }
}
