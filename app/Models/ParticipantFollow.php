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
}
