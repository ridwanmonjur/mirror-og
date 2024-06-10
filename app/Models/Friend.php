<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $table = 'friends';

    protected $fillable = ['user1_id', 'user2_id', 'status', 'actor_id'];

    public static function checkFollow($userProfileId, $logged_user_id)
    {
        return self::where(function ($query) use ($userProfileId, $logged_user_id) {
            $query->where('participant1_user_id', $userProfileId)
                ->where('participant2_user_id', $logged_user_id);
        })
            ->orWhere(function ($query) use ($userProfileId, $logged_user_id) {
                $query->where('participant2_user_id', $userProfileId)
                    ->where('participant1_user_id', $logged_user_id);
            })
            ->first();
    }
}
