<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinEvent extends Model
{
    use HasFactory;
    protected $table = 'join_events';

    public function eventDetail()
    {
        return $this->belongsTo(EventDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function eventDetails()
    {
        return $this->belongsTo(EventDetail::class, 'event_details_id', 'id');
    }

    public function eventTier()
    {
        return $this->hasOneThrough(EventTier::class, EventDetail::class, 'id', 'id', 'event_details_id', 'event_tier_id');
    }

    public static function getJoinEventsForTeam($team_id)
    {
        return self::whereHas('user.teams', function ($query) use ($team_id) {
            $query->where('team_id', $team_id);
        })
        ->with('user')
        ->get();
    }

    public static function getJoinEventsByTeamIdList($teamIdList)
    {
        return self::whereHas('user.teams', function ($query) use ($teamIdList) {
                $query->whereIn('team_id', $teamIdList);
            })
            ->with('user')
            ->get();
    }


}
