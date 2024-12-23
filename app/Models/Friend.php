<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopeWithBlockStatus($query, $loggedUserId, $userId)
    {
        return $query->leftJoin('blocks', function($join) use ($loggedUserId, $userId) {
            $join->where('blocks.user1_id', $loggedUserId)
                ->where('blocks.blocked_user_id', $userId);
        });
    }

    public function scopeWithReportStatus($query, $loggedUserId, $userId)
    {
        return $query->leftJoin('reports', function($join) use ($loggedUserId, $userId) {
            $join->where('reports.reporter_id', $loggedUserId)
                ->where('reports.reported_user_id', $userId);
        });
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

    public static function getFriendsPaginate($userId, $loggedUserId, $loggedUserRole, $perPage, $page = 1, $search = null)
    {
        return self::where(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                  ->orWhere('user2_id', $userId);
        })
        ->where('status', 'accepted')
        // ->leftJoin('friends as logged_user_friends', function($join) use ($loggedUserId) {
        //     $join->where(function($query) use ($loggedUserId) {
        //         $query->where('logged_user_friends.user1_id', $loggedUserId)
        //               ->orWhere('logged_user_friends.user2_id', $loggedUserId);
        //     })
        //     ->where('logged_user_friends.status', ['accepted', 'pending']);
        // })
        // ->leftJoin('blocks', function($join) use ($loggedUserId, $userId) {
        //     $join->where('block.user1_id', $loggedUserId)
        //         ->where('block.blocked_user_id', $userId);
        // })
        // ->leftJoin('reports', function($join) use ($loggedUserId, $userId) {
        //     $join->where('reports.reporter_id', $loggedUserId)
        //         ->where('reports.reported_user_id', $userId);
        // })
        // ->when($loggedUserRole === 'ORGANIZER', function ($query) use ($loggedUserId, $userId) {
        //     $query->leftJoin('organizer_follows', 'participant_user_id' , $loggedUserId);
        // })
        // ->when($loggedUserRole === 'PARTICIPANT', function ($query) use ($loggedUserId, $userId) {
        //     $query->leftJoin('participant_follows', 'prganizer_user_id' , $loggedUserId);
        // })
        
        ->where(function ($query) use ($search) {
            if ($search) {
                $query->whereHas('user1', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('user2', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }
        })
        ->with(['user1', 'user2'])
        ->simplePaginate($perPage, ['*'], 'friends_page', $page)
        ->through(function ($friend) use ($userId) {
            $relatedUser = $friend->user1_id != $userId ? $friend->user1 : $friend->user2;
            return [
                'id' => $relatedUser->id,
                'name' => $relatedUser->name,
                'email' => $relatedUser->email,
                'role' => $relatedUser->role,
                'userBanner' => $relatedUser->userBanner,
                'created_at' => $friend->created_at
            ];
        });
    }

    public static function getFriendCount(string| int $id, string $search = null) {
        return self::where(function ($query) use ($id) {
            $query->where('user1_id', $id)
                ->orWhere('user2_id', $id);
        })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('user1', function($q1) use ($search) {
                        $q1->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user2', function($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->where('status', 'accepted')
            ->count();
    }


}
