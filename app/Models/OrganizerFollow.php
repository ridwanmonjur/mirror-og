<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class OrganizerFollow extends Model
{
    use HasFactory;

    protected $table = 'organizer_follows';

    protected $fillable = [
        'participant_user_id',
        'organizer_user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participantUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_user_id', 'id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public static function getFollowCounts(array| null $userIds): array
    {
        return DB::table('users')
            ->leftJoin('organizer_follows', function ($q) {
                $q->on('users.id', '=', 'organizer_follows.organizer_user_id');
            })
            ->whereIn('users.id', $userIds)
            ->selectRaw('users.id as organizer_user_id, COALESCE(COUNT(organizer_follows.organizer_user_id), 0) as count')
            ->groupBy('users.id')
            ->pluck('count', 'organizer_user_id')
            ->toArray();
    }

    public static function getIsFollowing(int| string $userId, array| null $userIds): array
    {
        return DB::table('organizer_follows')
            ->where('participant_user_id', $userId)
            ->whereIn('organizer_user_id', $userIds)
            ->pluck('organizer_user_id', 'organizer_user_id')
            ->toArray();
    }

    public static function getFollowersCount($organizerUserId)
    {
        return self::where('organizer_user_id', $organizerUserId)->count();
    }

    public static function isFollowing($participantUserId, $organizerUserId)
    {
        return self::where('participant_user_id', $participantUserId)
            ->where('organizer_user_id', $organizerUserId)
            ->exists();
    }
}
