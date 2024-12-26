<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ParticipantFollow extends Model
{
    use HasFactory;

    protected $table = 'participant_follows';

    protected $fillable = [
        'participant_follower',
        'participant_followee',
    ];

    public function followerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_follower', 'id');
    }

    public function followeeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_followee', 'id');
    }

    public static function addLoggedUserInfo($query, $loggedUserId)
    {
        return $query->addSelect([
            'logged_user_friends.id as friend_id',
            'logged_user_friends.status as logged_friendship_status',
            DB::raw('COALESCE ( og_follows.id, p_follows.id ) as logged_follow_status'),
            'blocks.id as logged_block_status',
        ])
        ->leftJoin('friends as logged_user_friends', function($join) use ($loggedUserId) {
            $join->whereIn('logged_user_friends.status', ['accepted', 'pending'])
                ->where(function($q) use ($loggedUserId) {
                    $q->where(function($inner) use ($loggedUserId) {
                        $inner->where('logged_user_friends.user1_id', $loggedUserId)
                            ->where('logged_user_friends.user2_id', '=', 'users.id');
                    })
                    ->orWhere(function($inner) use ($loggedUserId) {
                        $inner->where('logged_user_friends.user2_id', $loggedUserId)
                            ->where('logged_user_friends.user1_id', '=', 'users.id');
                    });
                });
        })
        ->leftJoin('blocks', function($join) use ($loggedUserId) {
            $join->where('blocks.user_id', $loggedUserId)
                ->where('blocks.blocked_user_id', '=', 'users.id');
        })
        ->selectRaw('EXISTS(SELECT 1 FROM reports WHERE reporter_id = ? AND reported_user_id = users.id) as logged_report_status', [$loggedUserId])
        ->leftJoin('organizer_follows as og_follows', function($join) use ($loggedUserId) {
            $join->on('og_follows.organizer_user_id', '=', 'users.id')
                ->where('og_follows.participant_user_id', '=', $loggedUserId)
                ->where('users.role', '=', 'ORGANIZER')
                ->select(['og_follows.organizer_user_id', 'og_follows.participant_user_id', 'og_follows.id']);
        })
        ->leftJoin('participant_follows as p_follows', function($join) use ($loggedUserId) {
            $join->on('p_follows.participant_followee', '=', 'users.id')
                ->where('p_follows.participant_follower', '=', $loggedUserId)
                ->where('users.role', '=', 'PARTICIPANT')
                ->select(['p_follows.organizer_user_id', 'p_follows.participant_user_id', 'p_follows.id']);
        });
    }

    public static function checkFollow(int| string $follower, int| string $followee): ?self
    {
        return self::where(function ($query) use ($follower, $followee) {
            $query->where('participant_follower', $follower)
                ->where('participant_followee', $followee);
        })

            ->first();
    }

    public static function getFollowersPaginate($userId, $loggedUseId, $perPage, $page = 1, $search = null)
    {
        $select = [
            'users.id',
            'users.name',
            'users.email',
            'users.userBanner',
            'users.created_at',
            'users.role',
            'participant_follows.participant_follower',
            'participant_follows.participant_followee',
        ];

        $followQuery = self::select($select)
            ->where('participant_follows.participant_followee', $userId)
            ->join('users', 'participant_follows.participant_follower', '=', 'users.id')
            ->when(trim($search), function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%" . trim($search) . "%"); 
            });

        self::addLoggedUserInfo($followQuery, $loggedUseId);

        return $followQuery->simplePaginate($perPage, ['*'], 'followers_page', $page);
    }

    public static function getBothOrganizerAndParticipantFollowQuery(int|string $userId,  $search = null) {
        $select = [
            'users.id',
            'users.name',
            'users.email',
            'users.userBanner',
            'users.created_at',
            'users.role',
        ];

        $organizerFollowers = DB::table('users')
            ->select([
                'organizer_follows.organizer_user_id', 
                'organizer_follows.participant_user_id',
                ...$select,
            ])
            ->when(trim($search), function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%" . $search . "%");
            })
            ->join('organizer_follows', function($join) use ($userId) {
                $join->on('organizer_follows.organizer_user_id', '=', 'users.id')
                    ->where('organizer_follows.participant_user_id', $userId);
            });

        $participantFollowers = DB::table('users')
            ->select([
                'participant_follows.participant_follower',
                'participant_follows.participant_followee',
                ...$select,
            ])
            ->where('users.name', 'LIKE', "%{$search}%")
            ->join('participant_follows', function($join) use ($userId) {
                $join->on('participant_follows.participant_followee', '=', 'users.id')
                    ->where('participant_follows.participant_follower', $userId);

            });
        
        return [$organizerFollowers, $participantFollowers];
    }

    public static function getFollowingPaginate(int|string $userId, int|string|null $loggedUserId, $perPage, $page = 1, $search = null)  
    {
        [$organizerFollowers, $participantFollowers] = self::getBothOrganizerAndParticipantFollowQuery($userId, $search);
        if($loggedUserId) {
            self::addLoggedUserInfo($organizerFollowers, $loggedUserId);
            self::addLoggedUserInfo($participantFollowers, $loggedUserId);
        }
        $followQuery = $organizerFollowers->union($participantFollowers);


    return $followQuery->simplePaginate($perPage, ['*'], 'following_page', $page);
}
    public static function getFollowerCount(string| int $id) {
        return self::join('users', function($join) use ($id) {
                $join->on('participant_follows.participant_follower', '=', 'users.id')
                    ->where('participant_follows.participant_followee', $id);
            })
            ->count();
    }

    public static function getFolloweeCount(string| int $id) {
        [$organizerFollowers, $participantFollowers] = self::getBothOrganizerAndParticipantFollowQuery($id, null);
        $followQuery = $organizerFollowers->union($participantFollowers);

        return $followQuery->count();
    }


}
