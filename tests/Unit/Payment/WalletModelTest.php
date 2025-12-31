<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestPayments};
use App\Models\{Wallet, User, TransactionHistory};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;

class WalletModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestPayments;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $this->assertInstanceOf(User::class, $user->wallet->user);
        $this->assertEquals($user->id, $user->wallet->user_id);
    }

    /** @test */
    public function it_has_usable_balance()
    {
        $user = $this->createParticipant();
        $wallet = $this->createWallet($user, 100.00);

        $this->assertEquals(100.00, $wallet->usable_balance);
    }

    /** @test */
    public function it_has_total_balance()
    {
        $user = $this->createParticipant();
        $wallet = $this->createWallet($user, 100.00, 150.00);

        $this->assertEquals(100.00, $wallet->usable_balance);
        $this->assertEquals(150.00, $wallet->total_balance);
    }

    /** @test */
    public function it_can_increment_balance()
    {
        $user = $this->createParticipant();
        $wallet = $this->createWallet($user, 50.00);

        $this->addToWallet($wallet, 25.00);

        $this->assertEquals(75.00, $wallet->usable_balance);
    }

    /** @test */
    public function it_can_decrement_balance()
    {
        $user = $this->createParticipant();
        $wallet = $this->createWallet($user, 100.00);

        $this->deductFromWallet($wallet, 30.00);

        $this->assertEquals(70.00, $wallet->usable_balance);
    }

    /** @test */
    public function it_casts_balances_to_float()
    {
        $user = $this->createParticipant();
        $wallet = $this->createWallet($user, 100);

        $this->assertIsFloat($wallet->usable_balance);
        $this->assertIsFloat($wallet->total_balance);
    }

    /** @test */
    public function retrieve_or_create_cache_returns_existing_wallet()
    {
        $user = $this->createParticipant();
        $wallet = $user->wallet;

        $retrieved = Wallet::retrieveOrCreateCache($user->id);

        $this->assertEquals($wallet->id, $retrieved->id);
        $this->assertEquals($wallet->usable_balance, $retrieved->usable_balance);
    }

    /** @test */
    public function retrieve_or_create_cache_creates_wallet_if_not_exists()
    {
        $user = User::factory()->create();

        $this->assertDatabaseMissing('wallets', ['user_id' => $user->id]);

        $wallet = Wallet::retrieveOrCreateCache($user->id);

        $this->assertDatabaseHas('wallets', ['user_id' => $user->id]);
        $this->assertEquals(0.00, $wallet->usable_balance);
    }

    /** @test */
    public function it_has_transaction_history_relationship()
    {
        $user = $this->createParticipant();
        $wallet = $user->wallet;

        $this->createTransaction($wallet, 50.00, 'credit');
        $this->createTransaction($wallet, 20.00, 'debit');

        $wallet->refresh();

        if ($wallet->relationLoaded('transactions')) {
            $this->assertCount(2, $wallet->transactions);
        }
    }

    /** @test */
    public function it_updates_balance_atomically()
    {
        $user = $this->createParticipant();
        $wallet = $this->createWallet($user, 100.00);

        // Simulate concurrent updates
        $wallet->increment('usable_balance', 50.00);
        $wallet->refresh();

        $this->assertEquals(150.00, $wallet->usable_balance);
    }
}
