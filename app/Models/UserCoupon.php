<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'amount',
        'description',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public $timestamps = null;

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
}