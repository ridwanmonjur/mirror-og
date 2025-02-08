<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TeamFollow extends Model
{
    use HasFactory;

    protected $table = 'team_follows';

    protected $fillable = [
        'user_id',
        'team_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function getFollowersPaginate($teamId, $loggedUserId, $perPage, $page = 1, $search = null)
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

        $teamQuery = self::select($select)->where('team_follows.team_id', $teamId)
            ->join('users', 'team_follows.user_id', '=', 'users.id')
            ->leftJoin('participants', function($join) {
                $join->on('participants.user_id', '=', 'users.id')
                     ->where('users.role', '=', 'PARTICIPANT');
            })

            ->when(trim($search), function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%" . trim($search) . "%");
            });
        
        if($teamQuery) {
            $teamQuery->addSelect([
                'logged_user_friends.actor_id as logged_friendship_actor',
                'logged_user_friends.id as friend_id',
                'logged_user_friends.status as logged_friendship_status',
                DB::raw('og_follows.id as og_follow_status'),
                DB::raw('p_follows.id as p_follow_status'),
                DB::raw('COALESCE ( og_follows.id, p_follows.id ) as logged_follow_status'),
                'blocks.id as logged_block_status',
                
            ])
            ->leftJoin('friends as logged_user_friends', function($join) use ($loggedUserId) {
                $join->on(function($q) use ($loggedUserId) {
                    $q->on('logged_user_friends.user2_id', '=', 'users.id')
                      ->where('logged_user_friends.user1_id', '=', $loggedUserId)
                      ->orWhere(function($query) use ($loggedUserId) {
                          $query->on('logged_user_friends.user1_id', '=', 'users.id')
                                ->where('logged_user_friends.user2_id', '=', $loggedUserId);
                      });
                });
            })
            ->leftJoin('blocks', function($join) use ($loggedUserId) {
                $join->on('blocks.blocked_user_id', '=', 'users.id')
                    ->where('blocks.user_id', $loggedUserId);
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
        
        return $teamQuery->simplePaginate($perPage, ['*'], 'team_followers_page', $page);
    }
}
