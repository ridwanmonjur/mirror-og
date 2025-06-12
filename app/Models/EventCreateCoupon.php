<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCreateCoupon extends Model
{
    use HasFactory;
    protected $fillable = [];

    protected $table = 'event_create_coupon';

    public static function createNoEventCreateCouponFeeObject(array $fee, string| null $eventPrizePool): array
    {
        $fee['discountFee'] = 0;
        $fee['entryFee'] = $eventPrizePool !== null ? (float) $eventPrizePool : 0.0;
        $fee['totalFee'] = $fee['finalFee'] = $fee['entryFee'] + $fee['entryFee'] * 0.2;
        $fee['discountId'] = $fee['discountName'] = $fee['discountType'] = $fee['discountAmount'] = null;

        return $fee;
    }

    private function generateCarbonDateTime($startDate, $startTime, $timeZone = 'UTC')
    {
        if ($startTime != null) {
            if (substr_count($startTime, ':') === 2) {
                $startTime = explode(':', $startTime);
                $startTime = $startTime[0].':'.$startTime[1];
            }
        }


        if ($startDate !== null && $startTime !== null) {
            return Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime, $timeZone) ?? null;
        }
        return null;
    }

    public static function createEventCreateCouponFeeObject(string| null $couponName, string| null $eventPrizePool): array
    {
        $fee = [];

        if (is_null($couponName) || is_null($eventPrizePool)) {
            $fee = self::createNoEventCreateCouponFeeObject($fee, $eventPrizePool);

            return [$fee, 'isEventCreateCouponApplied' => false, 'error' => null];
        }
        $discount = self::whereRaw('coupon = ?', [$couponName])->first();

        if (is_null($discount)) {
            $fee = self::createNoEventCreateCouponFeeObject($fee, $eventPrizePool);

            return [
                $fee,
                'isEventCreateCouponApplied' => false,
                'error' => "Sorry, your coupon named {$couponName} can't be found!",
            ];
        }

        $currentDateTime = Carbon::now()->utc();
        $startTime = $this->generateCarbonDateTime($discount->startDate, $discount->startTime);
        $endTime = $this->generateCarbonDateTime($discount->endDate, $discount->endTime);
        $fee['discountId'] = $discount->id;
        $fee['discountName'] = $discount->name;
        $fee['discountType'] = $discount->type;
        $fee['discountAmount'] = $discount->amount;

        if ($startTime < $currentDateTime && $endTime > $currentDateTime && $discount->isEnforced) {
            $fee['entryFee'] = (float) $eventPrizePool * 1000;
            $fee['totalFee'] = $fee['entryFee'] + $fee['entryFee'] * 0.2;
            $fee['discountFee'] = $discount->type === 'percent' ?
                $discount->amount / 100 * $fee['totalFee'] : $discount->amount;
            $fee['finalFee'] = $fee['totalFee'] - $fee['discountFee'];

            return [
                $fee,
                'isEventCreateCouponApplied' => true,
                'error' => null,
            ];
        }
        $fee = self::createNoEventCreateCouponFeeObject($fee, $eventPrizePool);

        return [
            $fee,
            'isEventCreateCouponApplied' => false,
            'error' => 'Your coupon has already expired or is not available right now!',
        ];
    }
}
