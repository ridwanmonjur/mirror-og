<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getJoinEventsForTeamWithEventsRosterResults($team_id)
    {
        return self::where('team_id', $team_id)
            ->with(['eventDetails',  'user', 'results', 'roster' => function ($q) {
                $q->with('user');
            }, 'eventDetails.tier', 'eventDetails.game', 'eventDetails.user'
            ])
            ->get();
    }

    public static function getJoinEventsForTeamListWithEventsRosterResults($teamIdList)
    {
        return self::whereIn('team_id', $teamIdList)
            ->with(['eventDetails',  'user', 'results', 'roster' => function ($q) {
                $q->with('user');
            }, 'eventDetails.tier', 'eventDetails.game', 'eventDetails.user'
            ])
            ->get();
    }

    public static function getJoinEventsCountForTeam($team_id)
    {
        return self::where('team_id', $team_id)
            ->count();
    }

    public static function getJoinEventsWinCountForTeam($team_id)
    {
        $joins = DB::table('event_join_results')
            ->whereIn('join_events_id', function($q) use ($team_id) {
                $q->select('id')
                    ->from('join_events')
                    ->where('team_id', $team_id);
            })
            ->get();
        
        $sumPositionOne = 0;
        $streak = 0;
        $maxStreak = 0;
        
        foreach ($joins as $join) {
            if ($join->position == 1) {
                $sumPositionOne++;
            } else {
                $maxStreak = max($maxStreak, $streak);
                $streak = 0;
            }
        
            $streak++;
        }
            
        return ['wins' => $sumPositionOne, 'streak' => $streak];
    }

    public static function getJoinEventsWinCountForTeamList($teamIdList)
    {
        $joins = DB::table('event_join_results')
            ->whereIn('join_events_id', function($q) use ($teamIdList) {
                $q->select('id')
                    ->from('join_events')
                    ->whereIn('team_id', $teamIdList);
            })
            ->get();
        
        $sumPositionOne = 0;
        $streak = 0;
        $maxStreak = 0;
        
        foreach ($joins as $join) {
            if ($join->position == 1) {
                $sumPositionOne++;
            } else {
                $maxStreak = max($maxStreak, $streak);
                $streak = 0;
            }
        
            $streak++;
        }
            
        return ['wins' => $sumPositionOne, 'streak' => $streak];
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

        $joinEventIds = $joinEvents->pluck('eventDetails.user.id')->toArray();

        return [
            $joinEventIds,
            $joinEvents,
        ];
    }

    public static function processEvents($events, $isFollowing) {
        $activeEvents = [];
        $historyEvents = [];
    
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

        // dd($events, $activeEvents, $historyEvents);
    
        return ['joinEvents'=> $events, 'activeEvents' => $activeEvents, 'historyEvents' => $historyEvents];
    }

    public static function hasJoinedByOtherTeamsForSameEvent($eventId, $userId) {
        return self::where('event_details_id', $eventId)
            ->where(function ($query) use ($userId) {
                $query->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereIn('status', ['accepted', 'pending']);
                });
            })
            ->exists();
      
    }
}
