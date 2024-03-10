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
        return $this->belongsTo(User::class, 'joiner_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function eventDetails()
    {
        return $this->belongsTo(EventDetail::class, 'event_details_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class, 'join_events_id', 'id');
    }

    public function roster()
    {
        return $this->hasMany(RosterMember::class, 'join_events_id', 'id');
    }

    public function results()
    {
        return $this->hasMany(EventJoinResults::class, 'join_events_id', 'id');
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
        ->with('user');
       
    }

    public static function getJoinEventsByTeamIdList($teamIdList)
    {
        return self::whereHas('user.teams', function ($query) use ($teamIdList) {
                $query->whereIn('team_id', $teamIdList);
            })
            ->with('user');
    }

    public static function saveJoinEvent($data)
    {
        $joint = new JoinEvent();
        $joint->team_id = $data['team_id'];
        $joint->joiner_id = $data['joiner_id'];
        $joint->joiner_participant_id = $data['joiner_participant_id'];
        $joint->event_details_id = $data['event_details_id'];
        $joint->save();
        return $joint;
    }

}
