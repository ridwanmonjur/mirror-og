<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public static function checkFollow(int| string $follower, int| string $followee): ?self
    {
        return self::where(function ($query) use ($follower, $followee) {
            $query->where('participant_follower', $follower)
                ->where('participant_followee', $followee);
        })

            ->first();
    }

    public static function getFollowersPaginate($userId, $perPage, $page = 1, $search = null)
    {
        return self::where('participant_followee', $userId)
            ->with(['followerUser' => function($query) {
                $query->select('id', 'name', 'email', 'userBanner', 'created_at', 'role');
            }])
            ->when($search, function($query) use ($search) {
                $query->whereHas('followerUser', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            })
            ->simplePaginate($perPage, ['*'], 'followers_page', $page)
            ->through(function ($follow) {
                return [
                    'id' => $follow->followerUser->id,
                    'name' => $follow->followerUser->name,
                    'email' => $follow->followerUser->email,
                    'userBanner' => $follow->followerUser->userBanner,
                    'created_at' => $follow->created_at,
                    'role' => $follow->followerUser->role,
                ];
            });
    }

    public static function getFollowingPaginate($userId, $perPage, $page = 1, $search = null)
    {
        return self::where('participant_follower', $userId)
            ->with(['followeeUser' => function($query) {
                $query->select('id', 'name', 'email', 'userBanner', 'created_at', 'role');
            }])
            ->when($search, function($query) use ($search) {
                $query->whereHas('followeeUser', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            })
            ->simplePaginate($perPage, ['*'], 'following_page', $page)
            ->through(function ($follow) {
                return [
                    'id' => $follow->followeeUser->id,
                    'name' => $follow->followeeUser->name,
                    'email' => $follow->followeeUser->email,
                    'userBanner' => $follow->followeeUser->userBanner,
                    'created_at' => $follow->created_at,
                    'role' => $follow->followeeUser->role,
                ];
            });
    }


}
