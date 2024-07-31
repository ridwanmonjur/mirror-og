<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrganizerFollow extends Model
{
    use HasFactory;

    protected $table = 'organizer_follows';

    protected $fillable = [
        'participant_user_id',
        'organizer_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participantUser()
    {
        return $this->belongsTo(User::class, 'participant_user_id', 'id');
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public static function getFollowCounts($userIds)
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

    public static function getIsFollowing($userId, $userIds)
    {
        return DB::table('organizer_follows')
            ->where('participant_user_id', $userId)
            ->whereIn('organizer_user_id', $userIds)
            ->pluck('organizer_user_id', 'organizer_user_id')
            ->toArray();
    }
}
