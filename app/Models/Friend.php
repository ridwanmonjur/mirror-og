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
}
