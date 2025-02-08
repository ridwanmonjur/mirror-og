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
        return $this->belongsTo(User::class, 'organizer_user_id');
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

    public static function getFollowersPaginate($userId, $loggedUserId, $perPage, $page = 1, $search = null)
    {
        $select = [
            'users.id',
            'users.name',
            'users.email',
            'users.userBanner',
            'users.created_at',
            'users.role',
            'participants.nickname as nickname',
        ];

        $ogQuery = self::select($select)
            ->where('organizer_follows.organizer_user_id', $userId)
            ->join('users', 'organizer_follows.participant_user_id', '=', 'users.id')
            ->leftJoin('participants', function($join) {
                $join->on('participants.user_id', '=', 'users.id')
                     ->where('users.role', '=', 'PARTICIPANT');
            })
            ->when(trim($search), function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%" . trim($search) . "%");
            });

        
        if($loggedUserId) {
            $ogQuery->addSelect([
                'logged_user_friends.id as friend_id',
                'logged_user_friends.status as logged_friendship_status',
                'logged_user_friends.actor_id as logged_friendship_actor',
                DB::raw('og_follows.id as og_follow_status'),
                DB::raw('p_follows.id as p_follow_status'),
                DB::raw('COALESCE ( og_follows.id, p_follows.id ) as logged_follow_status'),
                'blocks.id as logged_block_status',
                
            ])
            ->leftJoin('friends as logged_user_friends', function($join) use ($loggedUserId) {
                $join->on('logged_user_friends.user2_id', '=', 'users.id')
                    ->where('logged_user_friends.user1_id', $loggedUserId);
            })
            ->leftJoin('blocks', function($join) use ($loggedUserId) {
                $join->on('blocks.blocked_user_id', '=', 'users.id')
                     ->where('blocks.user_id', '=', $loggedUserId);
            })
            ->leftJoin('organizer_follows as og_follows', function($join) use ($loggedUserId) {
                $join->on('og_follows.organizer_user_id', '=', 'users.id')
                    ->where('og_follows.participant_user_id', $loggedUserId);
            })
            ->leftJoin('participant_follows as p_follows', function($join) use ($loggedUserId) {
                $join->on('p_follows.participant_followee', '=', 'users.id')
                    ->where('p_follows.participant_follower', $loggedUserId);
            });
        }

        return $ogQuery->simplePaginate($perPage, ['*'], 'org_followers_page', $page);

            
            
    }
}
