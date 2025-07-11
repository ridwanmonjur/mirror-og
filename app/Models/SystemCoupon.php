<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class SystemCoupon extends Model
{
    protected $table = "system_coupons";

    protected $fillable = [
        'code',
        'amount',
        'description',
        'is_active',
        'is_public',
        'expires_at',
        'for_type',
        'redeem_count',
        'discount_type'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'expires_at' => 'datetime',
        'amount' => 'integer',
        'redeem_count' => 'integer'
    ];

    public $timestamps = false;

    protected $appends = [
        'expires_at_human',
    ];

    public function getExpiresAtHumanAttribute()
    {
        return $this->expires_at ? 'Exp: ' . $this->expires_at->format('d M Y') : 'NO EXPIRY';
    }

    /**
     * Get the users who have redeemed this coupon.
     */
    public function redeemedBy()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot('redeemed_at');
    }

    /**
     * Check if the coupon is valid (active and not expired).
     */
    public function isValid()
    {
        return $this->is_active && 
               ($this->expires_at === null || $this->expires_at > now());
    }


    public function userCoupons(): HasMany
    {
        return $this->hasMany(UserCoupon::class, 'coupon_id');
    }

    public static function getIncrementedFee( string| null $eventPrizePool, $incrementPercent = 0.2) {
        $entryFee = $eventPrizePool !== null ? (float) $eventPrizePool : 0.0;
        return $entryFee + ($entryFee * $incrementPercent);
    }

    public static function emptyOrgCoupon(array $fee, string| null $eventPrizePool, float $incrementPercent = 0.2): array
    {
        $fee['discountFee'] = 0;
        $fee['entryFee'] = $eventPrizePool !== null ? (float) $eventPrizePool : 0.0;
        $fee['totalFee'] = $fee['finalFee'] = self::getIncrementedFee($fee['entryFee'], $incrementPercent);
        $fee['discountId'] = $fee['discountName'] = $fee['discountType'] = $fee['discountAmount'] = null;

        return $fee;
    }

    

    public static function loadCoupon(string| null $couponName, string| null $eventPrizePool, float $incrementPercent = 0.2, string $role = 'organizer', $userId = null): array
    {
        $fee = [];

        if (is_null($couponName) || is_null($eventPrizePool)) {
            $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

            return [$fee, 'isCouponApplied' => false, 'error' => null];
        }
        $discount = self::where('code', $couponName)
            ->where('for_type', $role)->first();

        if (is_null($discount)) {
            $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

            return [
                $fee,
                'isCouponApplied' => false,
                'error' => "Sorry, your coupon named {$couponName} can't be found!",
            ];
        }

        $currentDateTime = Carbon::now()->utc();
        $endTime = $discount->expires_at ? Carbon::parse($discount->expires_at)->utc() : null;
        $fee['discountId'] = $discount->id;
        $fee['discountName'] = $discount->description;
        $fee['discountType'] = $discount->discount_type ?? 'percent';
        $fee['discountAmount'] = $discount->amount;

        if (($endTime === null || $endTime > $currentDateTime) && $discount->is_active) {
            // Check redeem count validation if userId is provided
            if ($userId) {
                $userCoupon = UserCoupon::where('user_id', $userId)
                    ->where('coupon_id', $discount->id)
                    ->first();

                if (!$userCoupon && !$discount->is_public) {
                    $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);
                    return [
                        $fee,
                        'isCouponApplied' => false,
                        'error' => 'This coupon is not available for your account.',
                    ];
                }

                if ($userCoupon && $userCoupon->redeemable_count >= $discount->redeem_count) {
                    $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);
                    return [
                        $fee,
                        'isCouponApplied' => false,
                        'error' => 'You have already redeemed this coupon too many times.',
                    ];
                }
            }

            $fee['entryFee'] = (float) $eventPrizePool ;
            $fee['totalFee'] = self::getIncrementedFee($fee['entryFee'], $incrementPercent);
            $fee['discountFee'] = $fee['discountType'] === 'percent' ?
                $discount->amount / 100 * $fee['totalFee'] : $discount->amount;
            $fee['finalFee'] = $fee['totalFee'] - $fee['discountFee'];

            if ($fee['finalFee'] > config('constants.STRIPE.ZER0') && $fee['finalFee'] < config('constants.STRIPE.MINIMUM_RM')) {
                $fee['finalFee'] = config('constants.STRIPE.MINIMUM_RM');
                $fee['discountFee'] = $fee['totalFee'] - $fee['finalFee'];
            }

            return [
                $fee,
                'isCouponApplied' => true,
                'error' => null,
            ];
        }
        $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

        return [
            $fee,
            'isApplied' => false,
            'error' => 'Your coupon has already expired or is not available right now!',
        ];
    }

    public function orgFullCheckout(EventDetail $event, User $user, int $amount) {
        $historyId = TransactionHistory::insertGetId([
            'name' => "$event->eventName: Full Discount",
            'type' => 'Entry Fee',
            'link' => route('public.event.view', ['id'=> $event->id]),
            'amount' => 0,
            'summary' => 'Complete Discount',
            'date'=> DB::raw('NOW()'),
            'user_id' => $user->id
        ]);

        $paymentId = OrganizerPayment::insertGetId([
            'payment_amount' => 0,
            'discount_amount' => $amount,
            'user_id' => $user->id,
            'history_id' => $historyId,
            'payment_id' => null,
        ]);

        return $paymentId;
    }
}