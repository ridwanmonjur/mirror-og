<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantFollow extends Model
{
    use HasFactory;

    protected $table = 'participant_follows';

    protected $fillable = [
        'participant_follower',
        'participant_followee',
    ];

    public function followerUser()
    {
        return $this->belongsTo(User::class, 'participant_follower', 'id');
    }

    public function followeeUser()
    {
        return $this->belongsTo(User::class, 'participant_followee', 'id');
    }

    public static function checkFollow($follower, $followee)
    {
        return self::where(function ($query) use ($follower, $followee) {
            $query->where('participant_follower', $follower)
                ->where('participant_followee', $followee);
        })

            ->first();
    }
}
