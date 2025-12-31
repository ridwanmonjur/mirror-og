<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Withdrawal, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WithdrawalModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 100.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $withdrawal->user);
        $this->assertEquals($user->id, $withdrawal->user->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 250.50,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->assertEquals($user->id, $withdrawal->user_id);
        $this->assertEquals('250.50', $withdrawal->withdrawal);
        $this->assertEquals(Withdrawal::STATUS_PENDING, $withdrawal->status);
        $this->assertNotNull($withdrawal->requested_at);
    }

    /** @test */
    public function it_casts_withdrawal_to_decimal()
    {
        $user = $this->createParticipant();

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 100,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->assertIsString($withdrawal->withdrawal);
        $this->assertEquals('100.00', $withdrawal->withdrawal);
    }

    /** @test */
    public function it_casts_requested_at_to_datetime()
    {
        $user = $this->createParticipant();

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 100.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $withdrawal->requested_at);
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals('pending', Withdrawal::STATUS_PENDING);
        $this->assertEquals('approved', Withdrawal::STATUS_APPROVED);
        $this->assertEquals('rejected', Withdrawal::STATUS_REJECTED);
        $this->assertEquals('completed', Withdrawal::STATUS_COMPLETED);
    }

    /** @test */
    public function it_has_business_rule_constants()
    {
        $this->assertEquals(5.00, Withdrawal::MIN_AMOUNT);
        $this->assertEquals(5000.00, Withdrawal::MAX_DAILY_AMOUNT);
        $this->assertEquals(5000.00, Withdrawal::MAX_TRANSACTION_AMOUNT);
        $this->assertEquals(7, Withdrawal::PROCESSING_DAYS);
    }

    /** @test */
    public function it_can_scope_for_user()
    {
        $user = $this->createParticipant();
        $otherUser = $this->createParticipant();

        Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 100.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        Withdrawal::create([
            'user_id' => $otherUser->id,
            'withdrawal' => 200.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $userWithdrawals = Withdrawal::forUser($user->id)->get();

        $this->assertCount(1, $userWithdrawals);
        $this->assertEquals($user->id, $userWithdrawals->first()->user_id);
    }

    /** @test */
    public function it_can_scope_today()
    {
        $user = $this->createParticipant();

        Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 100.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 200.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now()->subDay(),
        ]);

        $todayWithdrawals = Withdrawal::today()->get();

        $this->assertCount(1, $todayWithdrawals);
    }

    /** @test */
    public function it_can_mark_as_completed()
    {
        $user = $this->createParticipant();

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 100.00,
            'status' => Withdrawal::STATUS_APPROVED,
            'requested_at' => now(),
        ]);

        $withdrawal->markAsCompleted();

        $this->assertEquals(Withdrawal::STATUS_COMPLETED, $withdrawal->fresh()->status);
    }

    /** @test */
    public function it_has_formatted_amount_attribute()
    {
        $user = $this->createParticipant();

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 1234.56,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->assertEquals('RM 1,234.56', $withdrawal->formatted_amount);
    }

    /** @test */
    public function it_can_check_daily_limit()
    {
        $user = $this->createParticipant();

        Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 2000.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $canWithdraw = Withdrawal::checkDailyLimit($user->id, 2000.00);
        $this->assertTrue($canWithdraw);

        $cannotWithdraw = Withdrawal::checkDailyLimit($user->id, 4000.00);
        $this->assertFalse($cannotWithdraw);
    }

    /** @test */
    public function it_can_get_remaining_daily_limit()
    {
        $user = $this->createParticipant();

        Withdrawal::create([
            'user_id' => $user->id,
            'withdrawal' => 1500.00,
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $remaining = Withdrawal::getRemainingDailyLimit($user->id);

        $this->assertEquals(3500.00, $remaining);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $withdrawal = new Withdrawal();

        $this->assertNull($withdrawal->timestamps);
    }
}
