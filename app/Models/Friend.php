<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Friend extends Model
{
    use HasFactory;

    public User | null $relatedUser = null;

    protected $table = 'friends';

    protected $fillable = ['user1_id', 'user2_id', 'status', 'actor_id'];

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id', 'id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id', 'id');
    }




    public static function checkFriendship(string|int $userProfileId, string|int $logged_user_id): ?self
    {
        return self::where(function ($query) use ($userProfileId, $logged_user_id) {
            $query->where('user1_id', $userProfileId)
                ->where('user2_id', $logged_user_id);
        })
            ->orWhere(function ($query) use ($userProfileId, $logged_user_id) {
                $query->where('user2_id', $userProfileId)
                    ->where('user1_id', $logged_user_id);
            })
            ->first();
    }

    public static function getFriendsPaginate(int|string $userId, int|string|null $loggedUserId, $perPage, int $page = 1, string $search = null)
    {
       $query = self::select([
           'friends.*',
           'other_user.id as id',
           'other_user.name as name',
           'other_user.email as email',
           'other_user.userBanner as userBanner',
           'other_user.created_at as created_at',
           'other_user.role as role',
       
       ])
       ->where(function($query) use ($userId) {
           $query->where('friends.user1_id', $userId)
                 ->orWhere('friends.user2_id', $userId);
       })
       ->where('friends.status', 'accepted')
       ->join('users as other_user', function($join) use ($userId) {
           $join->on('other_user.id', '=', DB::raw("CASE 
               WHEN friends.user1_id = {$userId} THEN friends.user2_id 
               ELSE friends.user1_id 
           END"));
       });
    
        $query->when(trim($search), function($q) use ($search) {
            $q->where('other_user.name', 'LIKE', "%" . $search . "%");
        });
    
       if($loggedUserId) {
            $query->addSelect([
                'logged_user_friends.id as friend_id',
                'logged_user_friends.status as logged_friendship_status',
                'logged_user_friends.actor_id as logged_friendship_actor',
                DB::raw('COALESCE ( og_follows.id, p_follows.id ) as logged_follow_status'),
                'blocks.id as logged_block_status',
                
            ])
            
            ->leftJoin('friends as logged_user_friends', function($join) use ($loggedUserId) {
                $join->on(function($q) use ($loggedUserId) {
                    $q->on('logged_user_friends.user2_id', '=', 'other_user.id')
                      ->where('logged_user_friends.user1_id', '=', $loggedUserId)
                      ->orWhere(function($query) use ($loggedUserId) {
                          $query->on('logged_user_friends.user1_id', '=', 'other_user.id')
                                ->where('logged_user_friends.user2_id', '=', $loggedUserId);
                      });
                });
            })
           ->leftJoin('blocks', function($join) use ($loggedUserId) {
               $join->on('blocks.blocked_user_id', '=', 'other_user.id')
                   ->where('blocks.user_id', $loggedUserId);
           })
           ->selectRaw('EXISTS(SELECT 1 FROM reports WHERE reporter_id = ? AND reported_user_id = other_user.id) as logged_block_status', [$loggedUserId])
           ->leftJoin('organizer_follows as og_follows', function($join) use ($loggedUserId) {
               $join->on('og_follows.organizer_user_id', '=', 'other_user.id')
                   ->where('og_follows.participant_user_id', $loggedUserId);
           })
           ->leftJoin('participant_follows as p_follows', function($join) use ($loggedUserId) {
               $join->on('p_follows.participant_followee', '=', 'other_user.id')
                   ->where('p_follows.participant_follower', $loggedUserId);
           });
       }
    
       return $query->simplePaginate($perPage, ['*'], 'friends_page', $page);
    }

    public static function getFriendCount(string| int $id) {
        return self::where(function ($query) use ($id) {
            $query->where('user1_id', $id)
                ->orWhere('user2_id', $id);
        })
            ->where('status', 'accepted')
            ->count();
    }


}
