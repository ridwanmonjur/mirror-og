<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCoupon extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
        'redeemed_at',
        'redeemable_count'
    ];

    protected $table = "user_coupons";

    protected $casts = [
        'redeemed_at' => 'datetime'
    ];

    public $timestamps = NULL;

    /**
     * Get the user who redeemed the coupon.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coupon that was redeemed.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(SystemCoupon::class, 'coupon_id');
    }
}
