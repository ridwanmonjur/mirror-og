<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $table = 'friends';

    protected $fillable = ['user1_id', 'user2_id', 'status', 'actor_id'];

    public static function checkFriendship($userProfileId, $logged_user_id)
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
