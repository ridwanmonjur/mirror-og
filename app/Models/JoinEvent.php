<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JoinEvent extends Model
{
    use HasFactory;

    protected $table = 'join_events';

    public function eventDetail(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'joiner_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function eventDetails(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class, 'event_details_id', 'id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'team_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'team_id');
    }

    public function roster(): HasMany
    {
        return $this->hasMany(RosterMember::class, 'join_events_id', 'id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(EventJoinResults::class, 'join_events_id', 'id');
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
            }, 'eventDetails.tier', 'eventDetails.game', 'eventDetails.user',
            ])
            ->get();
    }

    public static function getJoinEventsForTeamListWithEventsRosterResults($teamIdList)
    {
        return self::whereIn('team_id', $teamIdList)
            ->with(['eventDetails',  'user', 'results', 'roster' => function ($q) {
                $q->with('user');
            }, 'eventDetails.tier', 'eventDetails.game', 'eventDetails.user',
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
            ->whereIn('join_events_id', function ($q) use ($team_id) {
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
            ->whereIn('join_events_id', function ($q) use ($teamIdList) {
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
    
    public static function fetchJoinEvents($teamId, $invitationListIds = [], $eventId = null)
    {
        $fixJoinEvents = function(Collection $eventList): array
        {
            $organizerIdList = $eventIdList = [];

            $eventList->each(function ($event) use (&$organizerIdList, &$eventIdList) {
                $organizerIdList[] = $event->eventDetails->user_id;
                $eventIdList[] = $event->id;
            });

            return [ $eventIdList, $organizerIdList ];
        };

        $query = static::where('team_id', $teamId);
        $invitedEvents = $joinEvents = collect();
        $invitedEventOrganizerIds = $joinEventOrganizerIds = $invitedIds = $joinIds = []; 
        $withClause = [
            'eventDetails', 'eventDetails.tier', 'eventDetails.user', 'eventDetails.game', 'members.payments', 'members.user',
        ];

        if (!is_null($eventId)) {
            $joinEvents = $query->where('event_details_id', $eventId)->get();
            $invitedEvents = null;
        } else {

            $joinEvents = $query->whereNotIn('event_details_id', $invitationListIds)->with($withClause)->get();
            $invitedEvents =  static::where('team_id', $teamId)
                ->whereIn('event_details_id', $invitationListIds)->with($withClause)
                ->get();
        }
        [$joinIds, $joinEventOrganizerIds] = $fixJoinEvents($joinEvents);
        [$invitedIds, $invitedEventOrganizerIds] = $fixJoinEvents($invitedEvents);
        $eventIds = [...$joinIds, ...$invitedIds];

        if (!is_null($eventId)) {
            $groupedPaymentsByEvent = $groupedPaymentsByEventAndTeamMember = [];
        } else {
            $groupedPaymentsByEvent =  ParticipantPayment::select('join_events_id', DB::raw('SUM(payment_amount) as total_payment_amount'))
                ->whereIn('join_events_id', $eventIds)
                ->groupBy('join_events_id')
                ->get()
                ->pluck('total_payment_amount', 'join_events_id');
                
            $groupedPaymentsByEventAndTeamMember =  ParticipantPayment::select('join_events_id', 'team_members_id', DB::raw('SUM(payment_amount) as total_payment_amount'))
                ->whereIn('join_events_id', $eventIds)
                ->groupBy('join_events_id', 'team_members_id')
                ->get()
                ->groupBy('join_events_id')
                ->map(function ($group) {
                    return $group->pluck('total_payment_amount', 'team_members_id');
                });
        }

        return [ 
            $joinEventOrganizerIds, $joinEvents, $invitedEventOrganizerIds,
            $invitedEvents, $groupedPaymentsByEvent, $groupedPaymentsByEventAndTeamMember,
        ];
    }
    

    public static function processEvents(Collection $events, array $isFollowing): array
    {
        $activeEvents = collect();
        $historyEvents = collect();

        $events->each(function ($joinEvent) use ($isFollowing, $activeEvents, $historyEvents) {
            $joinEvent->status = $joinEvent->eventDetails->statusResolved();
            $joinEvent->tier = $joinEvent->eventDetails->tier;
            $joinEvent->game = $joinEvent->eventDetails->game;
            $joinEvent->isFollowing = array_key_exists($joinEvent->eventDetails->user_id, $isFollowing);

            if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING'])) {
                $activeEvents->push($joinEvent);
            } elseif ($joinEvent->status === 'ENDED') {
                $historyEvents->push($joinEvent);
            }
        });

        return [
            'joinEvents' => $events,
            'activeEvents' => $activeEvents,
            'historyEvents' => $historyEvents,
        ];
    }

    public static function hasJoinedByOtherTeamsForSameEvent(
        string| int $eventId, string| int $userId, string $status
    ): bool
    {
        return self::where('event_details_id', $eventId)
            ->where(function ($query) use ($userId, $status) {
                $query->whereHas('members', function ($query) use ($userId, $status) {
                    $query->where('user_id', $userId)->where('status', $status);
                });
            })
            ->exists();
    }

    public static function getJoinedByTeamsForSameEvent($eventId, $userId): ?self
    {
        return self::where('event_details_id', $eventId)
            ->where(function ($query) use ($userId) {
                $query->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId)->where('status', 'accepted');
                });
            })
            ->first();
    }
}
