<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JoinEvent extends Model
{
    use HasFactory;

    protected $table = 'join_events';

    protected $fillable = ['id', 'event_details_id', 'created_at', 
        'updated_at', 'team_id', 'joiner_id', 'joiner_participant_id',
        'payment_status', 'join_status', 'vote_ongoing', 'vote_starter_id',
        'roster_captain_id', 'register_time'
    ];



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'joiner_id', 'id');
    }

    public function voteStarter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vote_starter_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users');
    }

    public function eventDetails(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class, 'event_details_id', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    
    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'team_id');
    }

    public function roster(): HasMany
    {
        return $this->hasMany(RosterMember::class, 'join_events_id', 'id');
    }

    // public function rosterHistory(): HasMany
    // {
    //     return $this->hasMany(RosterHistory::class, 'join_events_id', 'id');
    // }

    public function results(): HasMany
    {
        return $this->hasMany(EventJoinResults::class, 'join_events_id', 'id');
    }

    public function captain(): BelongsTo 
    {
        return $this->belongsTo(RosterMember::class, 'roster_captain_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ParticipantPayment::class, 'join_events_id', 'id');
    }

    public function position(): HasOne 
    {
        return $this->hasOne(EventJoinResults::class, 'join_events_id', 'id');
    }
  

    public static function getJoinEventsForTeamWithEventsRosterResults(int|string $team_id): Collection
    {
        return self::where('team_id', $team_id)
            ->where('join_status', '<>', 'canceled')
            ->with(['eventDetails',  'user', 'roster' => function ($q) {
                $q->with('user');
            }, 'eventDetails.tier', 'eventDetails.game', 'eventDetails.type', 'eventDetails.user',
            ])
            ->get();
    }

    public static function getJoinEventsByRoster($userId): Collection
    {

        $joinIdList = RosterMember::where('user_id', $userId)
            ->select(['user_id', 'join_events_id'])->get()->pluck('join_events_id');
        return self::whereIn('id', $joinIdList)
            ->where('join_status', '<>', 'canceled')
            ->with(['eventDetails',  'user', 'roster' => function ($q) {
                $q->with('user');
            }, 'eventDetails.tier', 'eventDetails.type', 'eventDetails.game', 'eventDetails.user',
            ])
            ->get();
    }

 
    public static function getJoinEventsWinCountForTeam(int| string $team_id): array
    {
        $joins = DB::table('event_join_results')
            ->whereIn('join_events_id', function ($q) use ($team_id) {
                $q->select('id')
                    ->from('join_events')
                    ->where('join_status', '<>', 'canceled')
                    ->where('team_id', $team_id);
                }
            )
            ->get();

        $sumPositionOne = $streak = $maxStreak = 0;

        foreach ($joins as $join) {
            if ($join->position === 1) {
                $sumPositionOne++;
            } else {
                $maxStreak = max($maxStreak, $streak);
                $streak = 0;
            }

            $streak++;
        }

        return ['wins' => $sumPositionOne, 'streak' => $streak];
    }

    public static function getPlayerJoinEventsWinCountForTeamList(array $teamIdList = [], $userProfileId): array
    {
        $joins = DB::table('event_join_results')
        ->whereIn('join_events_id', function ($q) use ($teamIdList, $userProfileId) {
            $q->select('join_events.id')
                ->from('join_events')
                ->where('join_status', '<>', 'canceled')
                ->join('roster_members', 'join_events.id', '=', 'roster_members.join_events_id')
                ->where('roster_members.user_id', $userProfileId)
                ->whereIn('join_events.team_id', $teamIdList);
        })
        ->get();

        $sumPositionOne = $streak = $maxStreak = 0;

        foreach ($joins as $join) {
            if ($join->position === 1) {
                $sumPositionOne++;
            } else {
                $maxStreak = max($maxStreak, $streak);
                $streak = 0;
            }

            $streak++;
        }

        return ['wins' => $sumPositionOne, 'streak' => $streak];
    }


    public static function saveJoinEvent(array $data): JoinEvent
    {
        $joint = new JoinEvent();
        $joint->team_id = $data['team_id'];
        $joint->joiner_id = $data['joiner_id'];
        $joint->joiner_participant_id = $data['joiner_participant_id'];
        $joint->event_details_id = $data['event_details_id'];
        $joint->save();

        return $joint;
    }

    public static function fetchJoinEvents(
        int|string $teamId, Collection|array|null $invitationListIds = [], int|string|null $eventId = null
    ): array
    {
        $fixJoinEvents = function (Collection| null $eventList): array {
            $organizerIdList = $eventIdList = [];
            if ($eventList) {
                $eventList->each(function ($event) use (&$organizerIdList, &$eventIdList) {
                    $organizerIdList[] = $event->eventDetails->user_id;
                    $eventIdList[] = $event->id;
                });
            }

            return [ $eventIdList, $organizerIdList ];
        };

        $query = static::where('team_id', $teamId);
        $invitedEvents = collect(); 
        $joinEvents = collect();
        $invitedEventOrganizerIds = $joinEventOrganizerIds = $invitedIds = $joinIds = [];
        $withClause = [
            'eventDetails', 'eventDetails.tier', 'eventDetails.type', 'eventDetails.signup', 'eventDetails.user', 
            'eventDetails.game',
            'members' => function($q) {
                $q->where('status', 'accepted')
                    ->with('payments', 'user');
            },
            'roster', 'roster.user', 'voteStarter', 'captain',
        ];

       
            // dd("bye");
            $joinEvents = $query->whereNotIn('event_details_id', $invitationListIds)->with($withClause)->get();
            $invitedEvents = static::where('team_id', $teamId)
                ->whereIn('event_details_id', $invitationListIds)->with($withClause)
                ->get();
        [$joinIds, $joinEventOrganizerIds] = $fixJoinEvents($joinEvents);
        [$invitedIds, $invitedEventOrganizerIds] = $fixJoinEvents($invitedEvents);
        $eventIds = [...$joinIds, ...$invitedIds];
       
        $groupedPaymentsByEvent = ParticipantPayment::select('join_events_id', DB::raw('SUM(payment_amount) as total_payment_amount'))
            ->whereIn('join_events_id', $eventIds)
            ->groupBy('join_events_id')
            ->get()
            ->pluck('total_payment_amount', 'join_events_id');

        $groupedPaymentsByEventAndTeamMember = ParticipantPayment::select('join_events_id', 'team_members_id', DB::raw('SUM(payment_amount) as total_payment_amount'))
            ->whereIn('join_events_id', $eventIds)
            ->groupBy('join_events_id', 'team_members_id')
            ->get()
            ->groupBy('join_events_id')
            ->map(function ($group) {
                return $group->pluck('total_payment_amount', 'team_members_id');
            });

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

    public static function processJoins(Collection $events, array $isFollowing)
    {
        $activeEvents = collect();

        $events->each(function ($joinEvent) use ($isFollowing, $activeEvents) {
            $joinEvent->status = $joinEvent->eventDetails->statusResolved();
            $joinEvent->tier = $joinEvent->eventDetails->tier;
            $joinEvent->game = $joinEvent->eventDetails->game;
            $joinEvent->isFollowing = array_key_exists($joinEvent->eventDetails->user_id, $isFollowing);

            $activeEvents->push($joinEvent);
           
        });

        return $activeEvents;
    }

    public static function isPartOfRoster(
        string| int $eventId,
        string| int $userId,
    ): bool {
        return self::where('event_details_id', $eventId)
            ->whereNot('join_status', 'canceled')
            ->where(function ($query) use ($userId) {
                $query->whereHas('roster', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            })
            ->exists();
    }

    public static function getJoinedByTeamsForSameEvent(string| int $eventId, string| int| null $userId): ?self
    {
        if (!$userId) return null;
        return self::where('event_details_id', $eventId)
            ->where(function ($query) use ($userId) {
                $query->whereHas('roster', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            })
            ->first();
    }

    public function decideRosterLeaveVote(): array {

        $this->vote_ongoing = true;

        $stayVoteCount = $leaveVoteCount = $totalVoteCount 
            = $stayRatio = $leaveRatio = 0;

        $this->roster?->each(function ($rosterMember) use (
                &$stayVoteCount, 
                &$leaveVoteCount, 
                &$totalVoteCount
            ) {
                $totalVoteCount++;
                
                if ($rosterMember->vote_to_quit === 1) {
                    $leaveVoteCount++;
                } elseif ($rosterMember->vote_to_quit === 0){
                    $stayVoteCount++;
                }
            });
        
            if ($totalVoteCount != 0) {
            $stayRatio = $stayVoteCount / $totalVoteCount;
            $leaveRatio = $leaveVoteCount / $totalVoteCount;
        }
      
        if ($leaveRatio > 0.5) {
            $this->vote_ongoing = false;
            $this->join_status = "canceled";
        }

        if ($stayRatio >= 0.5) {
            $this->vote_ongoing = false;
        }

        $this->save();

        return [$leaveRatio, $stayRatio];
    }

    public function completePayment($status)
    {
        $this->payment_status = 'completed';
        $this->register_time = $status;
        $this->save();
    }

}
