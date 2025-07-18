<?php

namespace App\Listeners;

use App\Coupon;
use App\Jobs\UpdateCoupon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CartUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $coupon = session()->get('coupon');
        
        if ($coupon && isset($coupon['name'])) {
            $couponName = $coupon['name'];
            $couponModel = Coupon::where('code', $couponName)->first();

            dispatch_now(new UpdateCoupon($couponModel));
        }
    }
}
