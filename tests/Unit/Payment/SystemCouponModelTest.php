<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{SystemCoupon, UserCoupon, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SystemCouponModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'description' => 'Test Coupon',
            'is_active' => true,
            'is_public' => true,
            'expires_at' => now()->addDays(30),
            'for_type' => 'organizer',
            'redeemable_count' => 5,
            'discount_type' => 'percent',
        ]);

        $this->assertEquals('TEST123', $coupon->code);
        $this->assertEquals(1000, $coupon->amount);
        $this->assertEquals('Test Coupon', $coupon->description);
        $this->assertTrue($coupon->is_active);
        $this->assertTrue($coupon->is_public);
        $this->assertEquals('organizer', $coupon->for_type);
        $this->assertEquals(5, $coupon->redeemable_count);
        $this->assertEquals('percent', $coupon->discount_type);
    }

    /** @test */
    public function it_casts_boolean_attributes()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'is_active' => 1,
            'is_public' => 0,
            'for_type' => 'organizer',
        ]);

        $this->assertIsBool($coupon->is_active);
        $this->assertIsBool($coupon->is_public);
        $this->assertTrue($coupon->is_active);
        $this->assertFalse($coupon->is_public);
    }

    /** @test */
    public function it_casts_expires_at_to_datetime()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'expires_at' => now()->addDays(30),
            'for_type' => 'organizer',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $coupon->expires_at);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $coupon = new SystemCoupon();

        $this->assertFalse($coupon->timestamps);
    }

    /** @test */
    public function it_has_expires_at_human_attribute()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'expires_at' => now()->addDays(30),
            'for_type' => 'organizer',
        ]);

        $this->assertStringContainsString('Exp:', $coupon->expires_at_human);
    }

    /** @test */
    public function it_shows_no_expiry_when_no_expiration_date()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'expires_at' => null,
            'for_type' => 'organizer',
        ]);

        $this->assertEquals('NO EXPIRY', $coupon->expires_at_human);
    }

    /** @test */
    public function it_has_many_user_coupons()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'for_type' => 'organizer',
        ]);

        UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemable_count' => 1,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $coupon->userCoupons);
        $this->assertCount(1, $coupon->userCoupons);
    }

    /** @test */
    public function it_belongs_to_many_users_through_redeemed_by()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'for_type' => 'organizer',
        ]);

        UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemed_at' => now(),
            'redeemable_count' => 1,
        ]);

        $redeemedUsers = $coupon->redeemedBy;

        $this->assertCount(1, $redeemedUsers);
        $this->assertEquals($user->id, $redeemedUsers->first()->id);
    }

    /** @test */
    public function it_validates_coupon_is_valid_when_active_and_not_expired()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
            'for_type' => 'organizer',
        ]);

        $this->assertTrue($coupon->isValid());
    }

    /** @test */
    public function it_validates_coupon_is_invalid_when_inactive()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'is_active' => false,
            'expires_at' => now()->addDays(30),
            'for_type' => 'organizer',
        ]);

        $this->assertFalse($coupon->isValid());
    }

    /** @test */
    public function it_validates_coupon_is_invalid_when_expired()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'is_active' => true,
            'expires_at' => now()->subDays(1),
            'for_type' => 'organizer',
        ]);

        $this->assertFalse($coupon->isValid());
    }

    /** @test */
    public function it_validates_coupon_is_valid_when_no_expiration()
    {
        $coupon = SystemCoupon::create([
            'code' => 'TEST123',
            'amount' => 1000,
            'is_active' => true,
            'expires_at' => null,
            'for_type' => 'organizer',
        ]);

        $this->assertTrue($coupon->isValid());
    }

    /** @test */
    public function it_calculates_incremented_fee()
    {
        $fee = SystemCoupon::getIncrementedFee('100', 0.2);

        $this->assertEquals(120.0, $fee);
    }

    /** @test */
    public function it_can_validate_and_increment_coupon_for_public_coupon()
    {
        $user = $this->createParticipant();

        $coupon = SystemCoupon::create([
            'code' => 'PUBLIC123',
            'amount' => 10,
            'is_public' => true,
            'is_active' => true,
            'redeemable_count' => 5,
            'for_type' => 'organizer',
        ]);

        $coupon->validateAndIncrementCoupon($user->id);

        $userCoupon = UserCoupon::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->first();

        $this->assertNotNull($userCoupon);
        $this->assertEquals(1, $userCoupon->redeemable_count);
    }
}
