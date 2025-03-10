<?php
namespace App\Services;

use App\Models\Achievements;
use App\Models\AwardResults;
use App\Models\EventDetail;
use App\Models\EventJoinResults;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\OrganizerFollow;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class EventMatchService {

    private $bracketDataService;

    public function __construct(BracketDataService $bracketDataService)
    {
        $this->bracketDataService = $bracketDataService;
    }

    public function generateBrackets(EventDetail $event, 
        bool $willFixBracketsAsOrganizer, 
        JoinEvent $existingJoint = null,
    ): array {
       
        $USER_ENUMS = config('constants.USER_ACCESS');

        $joinEventIds = $event->joinEvents->pluck('id');

        $event->load([
            'joinEvents.team.roster' => function ($query) use ($joinEventIds) {
                $query->select('id', 'team_id', 'join_events_id', 'user_id')
                      ->whereIn('join_events_id', $joinEventIds)
                      ->with(['user' => function ($query) {
                            $query->select('id', 'name', 'userBanner');
                        }]);
            },
            'matches',
        ]);

        $teamMap = collect();
        $teamList = collect();
        if ($willFixBracketsAsOrganizer) {
            $event->joinEvents->each(function ($joinEvent) use (&$teamList, &$teamMap) {
                $teamMap[$joinEvent->team->id] = $joinEvent->team;
                if ($joinEvent->join_status === 'confirmed') {
                    $teamList->push($joinEvent->team);
                }
            });
        } else {
            $event->joinEvents->each(function ($joinEvent) use (&$teamList, &$teamMap) {
                $teamMap[$joinEvent->team->id] = $joinEvent->team;
                if ($joinEvent->join_status !== 'canceled') {
                    $joinEvent->team->join_status = $joinEvent->join_status;
                    $teamList->push($joinEvent->team);
                }
            });
        }
       

        $matchTeamIds = collect();
        $event?->matches->each(function ($match) use ($teamMap, &$matchTeamIds) {
            $match->team1 = $teamMap->get($match->team1_id);
            $match->team2 = $teamMap->get($match->team2_id);
            $matchTeamIds->push($match->team1_id, $match->team2_id);
        });

        $matchesUpperCount = intval($event->tier?->tierTeamSlot); 
        if (!$matchesUpperCount) $previousValues = [];
        else {
            $previousValues = $this->bracketDataService::PREV_VALUES[$matchesUpperCount];
        } 

        $valuesMap = ['Tournament' => 'tournament', 'League' => 'tournament'];
        
        $tournamentType = $event->type?->eventType;
        if ($tournamentType) {
            $tournamentTypeFinal = $valuesMap[$tournamentType];
            $bracketList = $this->bracketDataService->produceBrackets(
                $matchesUpperCount, 
                $willFixBracketsAsOrganizer,
                $USER_ENUMS
            )[$tournamentTypeFinal];
            // dd($bracketList);
            $bracketList = $event->matches->reduce(function ($bracketList, $match) use (
                    $existingJoint, 
                    $willFixBracketsAsOrganizer,
                    $USER_ENUMS,
                ) {
                $path = "{$match->stage_name}.{$match->inner_stage_name}.{$match->order}";
                $user_level = $willFixBracketsAsOrganizer ? $USER_ENUMS['IS_ORGANIZER'] : null;
                
                if ($existingJoint) {
                    if ($match->team1_id === $existingJoint->team_id) { $user_level = $USER_ENUMS['IS_TEAM1']; }
                    elseif ($match->team2_id === $existingJoint->team_id) { $user_level = $USER_ENUMS['IS_TEAM2']; }
                }

                if (!$user_level) { $user_level = $USER_ENUMS['IS_PUBLIC']; }

                $match->user_level = $user_level;
                return data_set($bracketList, $path, [
                    'id' => $match->id,
                    'event_details_id' => $match->event_details_id,
                    'match_type' => $match->match_type,
                    'stage_name' => $match->stage_name,
                    'inner_stage_name' => $match->inner_stage_name,
                    'order' => $match->order,
                    'team1_id' => $match->team1_id,
                    'team2_id' => $match->team2_id,
                    'team1_teamBanner' => $match->team1?->teamBanner,
                    'team2_teamBanner' => $match->team2?->teamBanner,
                    'team1_teamName' => $match->team1?->teamName,
                    'team2_teamName' => $match->team2?->teamName,
                    'team1_roster' => $match->team1?->roster,
                    'team2_roster' => $match->team2?->roster,
                    'team1_position' => $match->team1_position,
                    'team2_position' => $match->team2_position,
                    'winner_id' => $match->winner_id,
                    'status' => $match->status,
                    'result' => $match->result,
                    'winner_next_position' => $match->winner_next_position,
                    'loser_next_position' => $match->loser_next_position,
                    'team1_name' => $match->team1->name ?? null,
                    'team2_name' => $match->team2->name ?? null,
                    'winner_name' => $match->winner->name ?? null,
                    'user_level'  => $match->user_level,
                ]);

            
            }, $bracketList);
         
        } else {
            $bracketList = [];
        };
        

        return [
            'teamList' => $teamList,
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList,
            'existingJoint' => $existingJoint,
            'previousValues' => $previousValues
        ];
    }

    public function getEventViewData(EventDetail $event, ?User $user, ?JoinEvent $existingJoint): array
    {
        $userId = $user?->id;
        
        $viewData = [
            'event' => $event,
            'awardAndTeamList' => AwardResults::getTeamAwardResults($event->id),
            'achievementsAndTeamList' => Achievements::getTeamAchievements($event->id),
            'joinEventAndTeamList' => EventJoinResults::getEventJoinResults($event->id),
            'followersCount' => OrganizerFollow::getFollowersCount($event->user_id),
            'likesCount' => Like::getLikesCount($event->id),
            'user' => $user,
            'existingJoint' => $existingJoint,
            'maxRosterSize' => config("constants.ROSTER_SIZE")  
        ];

        if ($user) {
            $viewData['user']->isFollowing = OrganizerFollow::isFollowing($userId, $event->user_id);
            $viewData['user']->isLiking = Like::isLiking($userId, $event->id);
        }

        return $viewData;
    }


}