<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotifcationsUser extends Model
{
    use HasFactory;
    protected $table = 'notifications2';
    protected $fillable = [
        'user_id',
        'type',
        'icon_type',
        'html',
        'link',
        'is_read',
        'img_src'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($notification) {
            $counter = NotificationCounter::firstOrCreate(
                ['user_id' => $notification->user_id]
            );
            $counter->incrementCounter($notification->type);
        });
    }

    public static function insertWithCount(array $notifications)
    {
        DB::transaction(function () use ($notifications) {
            self::insert($notifications);
            
            $counterUpdates = collect($notifications)
                ->groupBy('user_id')
                ->map(function ($userNotifications) {
                    return $userNotifications
                        ->mapToGroups(function ($notification) {
                            return [$notification['type'] => 1];
                        })
                        ->map(function ($counts) {
                            return $counts->sum();
                        });
                });
            
            foreach ($counterUpdates as $userId => $types) {
                NotificationCounter::firstOrCreate(['user_id' => $userId]);
                
                foreach ($types as $type => $count) {
                    if (is_string($type) && is_numeric($count)) {  
                        DB::table('notification_counters')
                            ->where('user_id', $userId)
                            ->increment("{$type}_count", (int)$count);
                    }
                }
            }
        });
    }

    public function markAsRead()
    {
        DB::transaction(function () {
            $updated = self::where('id', $this->id)
                ->where('is_read', false)
                ->lockForUpdate()
                ->update(['is_read' => true]);
            if ($updated > 0) {
                NotificationCounter::where('user_id', $this->user_id)
                    ->lockForUpdate()
                    ->decrement($this->type . '_count');
            }
            
        }, 3);
    }
     
}
