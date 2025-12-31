<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{UserCoupon, User, SystemCoupon};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCouponModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'for_type' => 'organizer',
        ]);

        $userCoupon = UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemable_count' => 1,
        ]);

        $this->assertInstanceOf(User::class, $userCoupon->user);
        $this->assertEquals($user->id, $userCoupon->user->id);
    }

    /** @test */
    public function it_belongs_to_system_coupon()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'for_type' => 'organizer',
        ]);

        $userCoupon = UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemable_count' => 1,
        ]);

        $this->assertInstanceOf(SystemCoupon::class, $userCoupon->coupon);
        $this->assertEquals($coupon->id, $userCoupon->coupon->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'for_type' => 'organizer',
        ]);

        $userCoupon = UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemed_at' => now(),
            'redeemable_count' => 3,
        ]);

        $this->assertEquals($user->id, $userCoupon->user_id);
        $this->assertEquals($coupon->id, $userCoupon->coupon_id);
        $this->assertNotNull($userCoupon->redeemed_at);
        $this->assertEquals(3, $userCoupon->redeemable_count);
    }

    /** @test */
    public function it_casts_redeemed_at_to_datetime()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'for_type' => 'organizer',
        ]);

        $userCoupon = UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemed_at' => now(),
            'redeemable_count' => 1,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $userCoupon->redeemed_at);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $userCoupon = new UserCoupon();

        $this->assertNull($userCoupon->timestamps);
    }

    /** @test */
    public function it_tracks_redeemable_count()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'for_type' => 'organizer',
        ]);

        $userCoupon = UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemable_count' => 0,
        ]);

        $userCoupon->increment('redeemable_count');

        $this->assertEquals(1, $userCoupon->fresh()->redeemable_count);
    }
}
