<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CountryRegion extends Model
{
    use HasFactory;

    public $timestamps = null;

    protected $table = 'countries_and_regions';
    
    protected $fillable = [
        'name',
        'emoji_flag',
        'type',
        'sort_order',
    ];

    // Cache key constant
    const CACHE_KEY = 'countries';
    const CACHE_DURATION = 36000; // 10 hours in seconds

    // Scopes
    
    /**
     * Get all countries and regions from cache
     */
    public static function getAllCached()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return self::select([   'name', 'id',
            'emoji_flag',
            'type',
            'sort_order'])->orderBy('sort_order')->get();
        });
    }

   
    public static function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
}