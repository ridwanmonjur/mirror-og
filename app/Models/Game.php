<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Game extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gameTitle',
        'gameIcon',
    ];

   

    
    // Cache key constant
    const CACHE_KEY = 'games';
    const CACHE_DURATION = 36000; // 10 hours in seconds

    // Scopes
    
    /**
     * Get all countries and regions from cache
     */
    public static function getAllCached()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return self::get();
        });
    }

   
    public static function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

}