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
        'is_read'
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
            // comment the line below and uncomment the next one
            if (!$notification->is_read) $counter->incrementCounter($notification->type);
            // $counter->incrementCounter($notification->type);
        });

        static::deleted(function ($notification) {
            if (!$notification->is_read) {
                $counter = NotificationCounter::where('user_id', $notification->user_id)->first();
                if ($counter) {
                    $counter->decrementCounter($notification->type);
                }
            }
        });
    }

    public function markAsRead()
    {
        return DB::transaction(function () {
            self::where('id', $this->id)
                ->where('is_read', false)
                ->lockForUpdate()
                ->update(['is_read' => true]);
            NotificationCounter::where('user_id', $this->user_id)
                ->lockForUpdate()
                ->decrement($this->type . '_count');
        }, 3);
    }
     
}
