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

    public static function getFriendsPaginate($userId, $perPage, $page = 1)
    {
        return self::where(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                  ->orWhere('user2_id', $userId);
        })
        ->where('status', 'accepted')
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

}
