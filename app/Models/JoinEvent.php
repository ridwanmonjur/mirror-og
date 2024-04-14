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

    public function teams()
    {
        return $this->hasMany(Team::class, 'team_id');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'team_id');
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

    public function participantPayments()
    {
        return $this->hasMany(ParticipantPayment::class, 'join_events_id');
    }

    public static function getJoinEventsForTeam($team_id)
    {
        return self::where('team_id', $team_id)
            ->with('user');
    }

    public static function getJoinEventsByTeamIdList($teamIdList)
    {
        return self::whereIn('team_id', $teamIdList)
            ->with('user');
    }

    public static function saveJoinEvent($data): JoinEvent
    {
        $joint = new JoinEvent();
        $joint->team_id = $data['team_id'];
        $joint->joiner_id = $data['joiner_id'];
        $joint->joiner_participant_id = $data['joiner_participant_id'];
        $joint->event_details_id = $data['event_details_id'];
        $joint->save();
        return $joint;
    }

    public static function getJoinEventsAndIds($teamId, $invitationListIds, $whereIn = true)
    {
        $query = static::where('team_id', $teamId);

        if ($whereIn) {
            $query->whereIn('event_details_id', $invitationListIds);
        } else {
            $query->whereNotIn('event_details_id', $invitationListIds);
        }

        $joinEvents = $query->with([
                'eventDetails', 'eventDetails.tier', 'eventDetails.user', 'eventDetails.game', 'participantPayments', 'participantPayments.members.user'
            ])
            ->withSum('participantPayments', 'payment_amount')
            ->groupBy('event_details_id')
            ->get();

        // dd($joinEvents);

        foreach ($joinEvents as $joinEvent) {
            $joinEvent->status = $joinEvent->eventDetails->statusResolved();
            $joinEvent->tier = $joinEvent->eventDetails->tier;
            $joinEvent->game = $joinEvent->eventDetails->game;
        }

        $joinEventIds = $joinEvents->pluck('id')->toArray();

        return [
            $joinEventIds,
            $joinEvents,
        ];
    }

    public static function processEvents($events, &$activeEvents, &$historyEvents, $isFollowing) {
        foreach ($events as $joinEvent) {
            $joinEvent->status = $joinEvent->eventDetails->statusResolved();
            $joinEvent->tier = $joinEvent->eventDetails->tier;
            $joinEvent->game = $joinEvent->eventDetails->game;
            $joinEvent->isFollowing = array_key_exists($joinEvent->eventDetails->user_id, $isFollowing);
            if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING'])) {
                $activeEvents[] = $joinEvent;
            } else if ($joinEvent->status == 'ENDED') {
                $historyEvents[] = $joinEvent;
            }
        }
    }

}
