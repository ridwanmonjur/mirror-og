<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NotificationCounter extends Model
{
    use HasFactory;

    protected $table = 'notification_counters';

    protected $fillable = [
        'user_id',
        'social_count',
        'teams_count',
        'event_count',
    ];

    protected $casts = [
        'social_count' => 'integer',
        'teams_count' => 'integer',
        'event_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function decrementCounter($type)
    {
        $result = DB::transaction(function () use ($type) {
            $columnName = "{$type}_count";

            return self::where('id', $this->id)
                ->where($columnName, '>', 0)
                ->lockForUpdate()
                ->update([$columnName => DB::raw($columnName.' - 1')]);
        }, 3);

        self::clearCache($this->user_id);
        return $result;
    }

    public function incrementCounter($type)
    {
        $result = DB::transaction(function () use ($type) {
            $columnName = "{$type}_count";

            return self::where('id', $this->id)
                ->lockForUpdate()
                ->update([$columnName => DB::raw($columnName.' + 1')]);
        }, 3);

        self::clearCache($this->user_id);
        return $result;
    }

    public static function getCachedCount($userId)
    {
        $cacheKey = "notification_count_{$userId}";

        return Cache::remember($cacheKey, config('cache.notification_counter_ttl', 3600), function () use ($userId) {
            return self::where('user_id', $userId)->first();
        });
    }

    public static function clearCache($userId)
    {
        Cache::forget("notification_count_{$userId}");
    }

    public static function resetNegativeCounts()
    {
        $affectedUserIds = self::query()
            ->where('social_count', '<', 1)
            ->orWhere('teams_count', '<', 1)
            ->orWhere('event_count', '<', 1)
            ->pluck('user_id');

        $result = self::query()
            ->where('social_count', '<', 1)
            ->orWhere('teams_count', '<', 1)
            ->orWhere('event_count', '<', 1)
            ->update([
                'social_count' => DB::raw('CASE WHEN social_count < 1 THEN 0 ELSE social_count END'),
                'teams_count' => DB::raw('CASE WHEN teams_count < 1 THEN 0 ELSE teams_count END'),
                'event_count' => DB::raw('CASE WHEN event_count < 1 THEN 0 ELSE event_count END'),
            ]);

        foreach ($affectedUserIds as $userId) {
            self::clearCache($userId);
        }

        return $result;
    }
}
