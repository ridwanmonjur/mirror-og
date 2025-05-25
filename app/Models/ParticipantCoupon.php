<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParticipantCoupon extends Model
{
    protected $table = "participant_coupons";

    protected $fillable = [
        'code',
        'amount',
        'description',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'amount' => 'integer'
    ];

    public $timestamps = null;

    protected $appends = [
        'expires_at_human',
    ];

    public function getExpiresAtHumanAttribute()
    {
        return $this->expires_at ? 'Exp: ' . $this->expires_at->format('d M Y') : 'Exp: NO EXPIRY';
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
}