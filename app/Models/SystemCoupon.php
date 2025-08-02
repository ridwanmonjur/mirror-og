<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class SystemCoupon extends Model
{
    protected $table = 'system_coupons';

    protected $fillable = [
        'code',
        'amount',
        'description',
        'is_active',
        'is_public',
        'expires_at',
        'for_type',
        'redeemable_count',
        'discount_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'expires_at' => 'datetime',
        'amount' => 'integer',
        'redeemable_count' => 'integer',
    ];

    public $timestamps = false;

    protected $appends = [
        'expires_at_human',
    ];

    public function getExpiresAtHumanAttribute()
    {
        return $this->expires_at ? 'Exp: '.$this->expires_at->format('d M Y') : 'NO EXPIRY';
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

    public static function getIncrementedFee(?string $eventPrizePool, $incrementPercent = 0.2)
    {
        $entryFee = $eventPrizePool !== null ? (float) $eventPrizePool : 0.0;

        return $entryFee + ($entryFee * $incrementPercent);
    }

    public static function emptyOrgCoupon(array $fee, ?string $eventPrizePool, float $incrementPercent = 0.2): array
    {
        $fee['discountFee'] = 0;
        $fee['entryFee'] = $eventPrizePool !== null ? (float) $eventPrizePool : 0.0;
        $fee['totalFee'] = $fee['finalFee'] = self::getIncrementedFee($fee['entryFee'], $incrementPercent);
        $fee['discountId'] = $fee['discountName'] = $fee['discountType'] = $fee['discountAmount'] = null;

        return $fee;
    }

    public static function loadCoupon(?string $couponName, ?string $eventPrizePool, float $incrementPercent = 0.2, string $role = 'organizer', $userId = null): array
    {
        $fee = [];

        if (is_null($couponName) || is_null($eventPrizePool)) {
            $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

            return [$fee, false, null, null];
        }
        $coupon = self::where('code', $couponName)
            ->where('for_type', $role)->first();

        if (is_null($coupon)) {
            $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

            return [
                $fee,
                false,
                "Sorry, your coupon named {$couponName} can't be found!",
                null,
            ];
        }

        $currentDateTime = Carbon::now()->utc();
        $endTime = $coupon->expires_at ? Carbon::parse($coupon->expires_at)->utc() : null;
        $fee['discountId'] = $coupon->id;
        $fee['discountName'] = $coupon->description;
        $fee['discountType'] = $coupon->discount_type ?? 'percent';
        $fee['discountAmount'] = $coupon->amount;

        if (($endTime === null || $endTime > $currentDateTime) && $coupon->is_active) {
            // Check redeem count validation if userId is provided
            if ($userId) {
                $userCoupon = UserCoupon::where('user_id', $userId)
                    ->where('coupon_id', $coupon->id)
                    ->first();

                if (! $userCoupon && ! $coupon->is_public) {
                    $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

                    return [
                        $fee,
                        false,
                        'This coupon is not available for your account.',
                        null,
                    ];
                }

                if ($userCoupon && $userCoupon->redeemable_count >= $coupon->redeemable_count) {
                    $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

                    return [
                        $fee,
                        false,
                        'You have already redeemed this coupon too many times.',
                        null,
                    ];
                }
            }

            $fee['entryFee'] = (float) $eventPrizePool;
            $fee['totalFee'] = self::getIncrementedFee($fee['entryFee'], $incrementPercent);
            $fee['discountFee'] = $fee['discountType'] === 'percent' ?
                $coupon->amount / 100 * $fee['totalFee'] : $coupon->amount;
            $fee['finalFee'] = $fee['totalFee'] - $fee['discountFee'];

            if ($fee['finalFee'] > config('constants.STRIPE.ZER0') && $fee['finalFee'] < config('constants.STRIPE.MINIMUM_RM')) {
                $fee['finalFee'] = config('constants.STRIPE.MINIMUM_RM');
                $fee['discountFee'] = $fee['totalFee'] - $fee['finalFee'];
            }

            return [
                $fee,
                true,
                null,
                null,
            ];
        }
        $fee = self::emptyOrgCoupon($fee, $eventPrizePool, $incrementPercent);

        return [
            $fee,
            'isApplied' => false,
            'error' => 'Your coupon has already expired or is not available right now!',
            $coupon,
        ];
    }

    public function orgFullCheckout(EventDetail $event, User $user, int $amount)
    {
        $historyId = TransactionHistory::insertGetId([
            'name' => "$event->eventName: Full Discount",
            'type' => 'Entry Fee',
            'link' => route('public.event.view', ['id'=> $event->id]),
            'amount' => 0,
            'summary' => 'Complete Discount',
            'date'=> DB::raw('NOW()'),
            'user_id' => $user->id,
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

    public static function participantPay($event, $user, $fee, $status, $joinEvent, $memberId)
    {
        $isEarly = $status == config('constants.SIGNUP_STATUS.EARLY');

        if ($isEarly) {
            $history = new TransactionHistory([
                'name' => "{$event->eventName}",
                'type' => "Top up for Event: RM {$fee['totalFee']}",
                'link' => route('public.event.view', ['id' => $event->id]),
                'amount' => $fee['totalFee'],
                'summary' => "{$event->game->gameTitle}, {$event->tier->eventTier}, {$event->type->eventType}",
                'isPositive' => false,
                'date' => now(),
                'user_id' => $user->id,
            ]);
        } else {
            $history = new TransactionHistory([
                'name' => "{$event->eventName}",
                'type' => "Top up: RM {$fee['totalFee']}",
                'link' => null,
                'amount' => $fee['totalFee'],
                'summary' => "Wallet RM {$fee['totalFee']}",
                'isPositive' => false,
                'date' => now(),
                'user_id' => $user->id,
            ]);
        }

        $history?->save();

        ParticipantPayment::create([
            'team_members_id' => $memberId,
            'user_id' => $user->id,
            'join_events_id' => $joinEvent->id,
            'payment_amount' => $fee['totalFee'],
            'payment_id' => null,
            'register_time' => $status,
            'history_id' => $history?->id,
            'type' => 'stripe',
        ]);
    }

    public function validateAndIncrementCoupon($userId)
    {

        if (! $this->is_public) {
            $userCoupon = UserCoupon::where('user_id', $userId)
                ->where('coupon_id', $this->id)
                ->first();

            if (! $userCoupon) {
                throw new Exception('You do not have access to this coupon.');
            }
        } else {
            $userCoupon = UserCoupon::firstOrCreate([
                'user_id' => $userId,
                'coupon_id' => $this->id,
            ], [
                'redeemable_count' => 0,
            ]);
        }

        $userCoupon = UserCoupon::where('user_id', $userId)
            ->where('coupon_id', $this->id)
            ->first();

        if ($userCoupon->redeemable_count >= $this->redeemable_count) {
            throw new Exception('You have exceeded the maximum number of redemptions for this coupon.');
        }

        $userCoupon->increment('redeemable_count');
    }
}
