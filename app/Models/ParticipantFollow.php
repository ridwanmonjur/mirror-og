<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ParticipantFollow extends Model
{
    use HasFactory;

    protected $table = "participant_follows";

    protected $fillable = [
        'participant1_user',
        'participant2_user',
    ];

    public static function checkFollow($userProfileId, $logged_user_id)
    {
        return self::where(function ($query) use ($userProfileId, $logged_user_id) {
            $query->where('participant1_user', $userProfileId)
                ->where('participant2_user', $logged_user_id);
        })
            ->orWhere(function ($query) use ($userProfileId, $logged_user_id) {
                $query->where('participant2_user', $userProfileId)
                    ->where('participant1_user', $logged_user_id);
            })
            ->first();
    }

    public static function getFollowCounts($userIds)
    {
        return DB::table('users')
            ->leftJoin('organizer_follows', function ($q) {
                $q->on('users.id', '=', 'follows.organizer_user_id');
            })
            ->whereIn('users.id', $userIds)
            ->selectRaw('users.id as organizer_user_id, COALESCE(COUNT(follows.organizer_user_id), 0) as count')
            ->groupBy('users.id')
            ->pluck('count', 'organizer_user_id')
            ->toArray();
    }

    public static function getIsFollowing($userId, $userIds)
    {
        return DB::table('organizer_follows')
            ->where('participant_user_id', $userId)
            ->whereIn('organizer_user_id', $userIds)
            ->pluck('organizer_user_id', 'organizer_user_id')
            ->toArray();
    }
}
