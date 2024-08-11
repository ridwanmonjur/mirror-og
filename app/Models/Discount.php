<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable = [];

    protected $table = 'organizer_create_event_discounts';

    public static function createNoDiscountFeeObject(array $fee, string $entryFee): array
    {
        $fee['discountFee'] = 0;
        $fee['entryFee'] = (float) $entryFee * 1000;
        $fee['totalFee'] = $fee['finalFee'] = $fee['entryFee'] + $fee['entryFee'] * 0.2;
        $fee['discountId'] = $fee['discountName'] = $fee['discountType'] = $fee['discountAmount'] = null;

        return $fee;
    }

    public static function createDiscountFeeObject(string| null $couponName, string $eventTierEntryFee): array
    {
        $fee = [];

        if (is_null($couponName)) {
            $fee = self::createNoDiscountFeeObject($fee, $eventTierEntryFee);

            return [$fee, 'isDiscountApplied' => false, 'error' => null];
        }
        $discount = self::whereRaw('coupon = ?', [$couponName])->first();

        if (is_null($discount)) {
            $fee = self::createNoDiscountFeeObject($fee, $eventTierEntryFee);

            return [
                $fee,
                'isDiscountApplied' => false,
                'error' => "Sorry, your coupon named {$couponName} can't be found!",
            ];
        }

        $currentDateTime = Carbon::now()->utc();
        $startTime = generateCarbonDateTime($discount->startDate, $discount->startTime);
        $endTime = generateCarbonDateTime($discount->endDate, $discount->endTime);
        $fee['discountId'] = $discount->id;
        $fee['discountName'] = $discount->name;
        $fee['discountType'] = $discount->type;
        $fee['discountAmount'] = $discount->amount;

        if ($startTime < $currentDateTime && $endTime > $currentDateTime && $discount->isEnforced) {
            $fee['entryFee'] = (float) $eventTierEntryFee * 1000;
            $fee['totalFee'] = $fee['entryFee'] + $fee['entryFee'] * 0.2;
            $fee['discountFee'] = $discount->type === 'percent' ?
                $discount->amount / 100 * $fee['totalFee'] : $discount->amount;
            $fee['finalFee'] = $fee['totalFee'] - $fee['discountFee'];

            return [
                $fee,
                'isDiscountApplied' => true,
                'error' => null,
            ];
        }
        $fee = self::createNoDiscountFeeObject($fee, $eventTierEntryFee);

        return [
            $fee,
            'isDiscountApplied' => false,
            'error' => 'Your coupon has already expired or is not available right now!',
        ];
    }
}
