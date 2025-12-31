<?php

namespace Tests\Unit\Payment;

use Tests\TestCase;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class CouponModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_find_by_code()
    {
        DB::table('coupons')->insert([
            'code' => 'TEST123',
            'type' => 'fixed',
            'value' => 10,
        ]);

        $coupon = Coupon::findByCode('TEST123');

        $this->assertNotNull($coupon);
        $this->assertEquals('TEST123', $coupon->code);
    }

    /** @test */
    public function it_returns_null_when_code_not_found()
    {
        $coupon = Coupon::findByCode('NONEXISTENT');

        $this->assertNull($coupon);
    }

    /** @test */
    public function it_calculates_fixed_discount()
    {
        DB::table('coupons')->insert([
            'code' => 'FIXED10',
            'type' => 'fixed',
            'value' => 10,
        ]);

        $coupon = Coupon::findByCode('FIXED10');
        $discount = $coupon->discount(100);

        $this->assertEquals(10, $discount);
    }

    /** @test */
    public function it_calculates_percent_discount()
    {
        DB::table('coupons')->insert([
            'code' => 'PERCENT20',
            'type' => 'percent',
            'percent_off' => 20,
        ]);

        $coupon = Coupon::findByCode('PERCENT20');
        $discount = $coupon->discount(100);

        $this->assertEquals(20, $discount);
    }

    /** @test */
    public function it_returns_zero_for_unknown_type()
    {
        DB::table('coupons')->insert([
            'code' => 'UNKNOWN',
            'type' => 'unknown_type',
        ]);

        $coupon = Coupon::findByCode('UNKNOWN');
        $discount = $coupon->discount(100);

        $this->assertEquals(0, $discount);
    }

    /** @test */
    public function it_rounds_percent_discount()
    {
        DB::table('coupons')->insert([
            'code' => 'PERCENT15',
            'type' => 'percent',
            'percent_off' => 15,
        ]);

        $coupon = Coupon::findByCode('PERCENT15');
        $discount = $coupon->discount(100);

        $this->assertEquals(15, $discount);
    }
}
