<?php
namespace App\Services;

use App\Models\Achievements;
use App\Models\AwardResults;
use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\EventJoinResults;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\Matches;
use App\Models\OrganizerFollow;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class EventMatchService {

    private $bracketDataService;

    public function __construct(BracketDataService $bracketDataService)
    {
        $this->bracketDataService = $bracketDataService;
    }

    public function createBrackets (EventDetail $event) {
        // dd($event);
        if (!isset($event->matches[0]) && $event?->tier?->tierTeamSlot) {
            $bracketList = $this->bracketDataService->produceBrackets(
                $event->tier->tierTeamSlot, 
                false,
                null, 
                null
            );

            $now = now();
            $matches = [];
            foreach ($bracketList as $stage_name => $stage_element) {
                foreach ($stage_element as $inner_stage_name => $inner_stage_element) {
                    foreach ($inner_stage_element as $order => $element) {
                        $matches[] = [
                            'order' => $order,
                            'event_details_id' => $event->id,
                            'stage_name' => $stage_name,
                            'inner_stage_name' => $inner_stage_name,
                            'team1_position' => $element['team1_position'],
                            'team2_position' => $element['team2_position'],
                            'team1_id' => null,
                            'team2_id' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }


            Matches::insert($matches);
        }
    }

    public function generateBrackets(EventDetail $event, 
        bool $willFixBracketsAsOrganizer, 
        JoinEvent | null $existingJoint,
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
        $event->joinEvents->each(function ($joinEvent) use (&$teamList, &$teamMap) {
            $teamMap[$joinEvent->team->id] = $joinEvent->team;
            if ($joinEvent->join_status === 'confirmed') {
                $teamList->push($joinEvent->team);
            }
        });

        $deadlines = BracketDeadline::getByEventDetail($event->id, $event->tier?->tierTeamSlot);
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

        
       
            $bracketList = $this->bracketDataService->produceBrackets(
                $matchesUpperCount, 
                $willFixBracketsAsOrganizer,
                $USER_ENUMS, 
                $deadlines
            );

            // dd($bracketList);

            $bracketList = $event?->matches?->reduce(function ($bracketList, $match) use (
                $existingJoint, 
                $willFixBracketsAsOrganizer,
                $USER_ENUMS,
            ) {
                $path = "{$match->stage_name}.{$match->inner_stage_name}.{$match->order}";
                $user_level = null;
                if ($existingJoint) {
                    if ($match->team1_id === $existingJoint->team_id) { $user_level = $USER_ENUMS['IS_TEAM1']; }
                    elseif ($match->team2_id === $existingJoint->team_id) { $user_level = $USER_ENUMS['IS_TEAM2']; }
                } else {
                    $user_level = $willFixBracketsAsOrganizer ? $USER_ENUMS['IS_ORGANIZER'] :  $USER_ENUMS['IS_PUBLIC'];
                }
            
                $existingData = data_get($bracketList, $path, []);
                
                $updatedProperties = [
                    'id' => $match->id,
                    'event_details_id' => $match->event_details_id,
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
                    'team1_name' => $match->team1->name ?? null,
                    'team2_name' => $match->team2->name ?? null,
                    'user_level' => $user_level,
                    // 'deadline' => $existingData['deadline']
                ];
                
                $mergedData = array_merge($existingData, $updatedProperties);
                
                return data_set($bracketList, $path, $mergedData);
            }, $bracketList);
         
        
        
        return [
            'teamList' => $teamList,
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList,
            'existingJoint' => $existingJoint,
            'previousValues' => $previousValues,
            
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