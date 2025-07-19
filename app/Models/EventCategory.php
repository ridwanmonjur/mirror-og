<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class EventCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'event_categories';

    protected $casts = [
        'eventTags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    const CACHE_KEY = 'event_categories';
    const CACHE_DURATION = 36000; 

    /**
     * Get all event categories from cache
     */
    public static function getAllCached()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return self::get();
        });
    }

    /**
     * Clear the cache
     */
    public static function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
}
