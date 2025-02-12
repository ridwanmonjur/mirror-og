<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotificationCounter extends Model
{
    use HasFactory;
    protected $table = 'notification_counters';
    protected $fillable = [
        'user_id',
        'social_count',
        'teams_count',
        'event_count'
    ];

    protected $casts = [
        'social_count' => 'integer',
        'teams_count' => 'integer',
        'event_count' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function decrementCounter($type)
    {
        return DB::transaction(function () use ($type) {
            $columnName = "{$type}_count";
            
            return self::where('id', $this->id)
                ->where($columnName, '>', 0)
                ->lockForUpdate()
                ->update([$columnName => DB::raw($columnName . ' - 1')]);
        }, 3);
    }

    public function incrementCounter($type)
    {
        return DB::transaction(function () use ($type) {
            $columnName = "{$type}_count";
            
            return self::where('id', $this->id)
                ->lockForUpdate()
                ->update([$columnName => DB::raw($columnName . ' + 1')]);
        }, 3);
    }
}
