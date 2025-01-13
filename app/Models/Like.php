<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $fillable = ['user_id', 'event_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class, 'event_id', 'id');
    }

    public static function getLikesCount($eventId)
    {
        return self::where('event_id', $eventId)->count();
    }

    public static function isLiking($userId, $eventId)
    {
        return self::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->exists();
    }
}
