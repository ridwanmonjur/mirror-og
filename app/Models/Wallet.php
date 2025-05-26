<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'user_wallet';
    protected $fillable = [
        'usable_balance',
        'current_balance',
        'user_id',
        'has_bank_account',
        'bank_last4',
        'bank_name',
        'balance',
        'last_payout_at',
        'stripe_customer_id',
        'payment_method_id',
    ];

    public $timestamps = NULL;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'details_submitted' => 'boolean',
        'charges_enabled' => 'boolean',
        'has_bank_account' => 'boolean',
        'balance' => 'decimal:2',
        'last_payout_at' => 'datetime',
    ];

    public function isReadyForPayouts()
    {
        return $this->has_bank_account && $this->stripe_customer_id;
    }

    /**
     * Put - Store current model in cache
     */
    public function putCache()
    {
        $cacheKey = sprintf(config('cache.keys.wallet'), $this->user_id);
        $cacheTtl = config('cache.ttl');
        Cache::put($cacheKey, $this, $cacheTtl);
        
        return $this;
    }
    
    /**
     * Clear - Remove from cache only
     */
    public function clearCache()
    {
        $cacheKey = sprintf(config('cache.keys.wallet'), $this->user_id);
        Cache::forget($cacheKey);
        
        return $this;
    }
    
    public static function retrieveOrCreateCache($userId)
    {
        $cacheKey = sprintf(config('cache.keys.wallet'), $userId);
        $cacheTtl = config('cache.ttl');
        
        return Cache::remember($cacheKey, $cacheTtl, function () use ($userId) {
            return static::firstOrCreate(['user_id' => $userId]);
        });
    }

    protected static function booted()
    {
        static::saved(function ($wallet) {
            $wallet->putCache();
        });
        
        static::deleted(function ($wallet) {
            $wallet->clearCache();
        });
    }


}
