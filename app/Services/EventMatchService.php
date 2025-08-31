<?php

namespace App\Services;

use App\Models\Achievements;
use App\Models\AwardResults;
use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\EventJoinResults;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\Brackets;
use App\Models\OrganizerFollow;
use App\Models\RosterMember;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class EventMatchService
{

    public function createBrackets(EventDetail $event)
    {
        // dd($event);
        if (! isset($event->matches[0]) && $event?->tier?->tierTeamSlot && $event?->type?->eventType) {
            $eventType = $event->type->eventType;
            $dataService = DataServiceFactory::create($eventType);
            $page = 'all';
            
            $cacheKey = "{$eventType}_{$event->id}_{$event->tier->tierTeamSlot}_{$page}_0";
            $bracketList = Cache::remember($cacheKey, config('cache.ttl', 3600), function () use (
                $dataService, $event, $page
            ) {
                return $dataService->produceBrackets(
                    $event->tier->tierTeamSlot,
                    false,
                    null,
                    null,
                    $page
                );
            });


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

            Brackets::insert($matches);
        }
    }

    public function generateBrackets(EventDetail $event,
        bool $willFixBracketsAsOrganizer,
        ?JoinEvent $existingJoint,
        ?int $page = 1
    ): array {
        $USER_ENUMS = config('constants.USER_ACCESS');
        $DISPUTTE_ENUMS = config('constants.DISPUTE');
        $deadlineData = BracketDeadline::getByEventDetail($event->id, $event->tier?->tierTeamSlot);
        $deadlines = $deadlineData['deadlines'];
        $deadlinesHash = $deadlineData['hash'];
        
        $eventType = $event->type?->eventType;
        if (!$eventType) {
            return [
                'teamList' => collect(),
                'matchesUpperCount' => 0,
                'bracketList' => [],
                'existingJoint' => $existingJoint,
                'previousValues' => [],
                'DISPUTE_ACCESS' => $DISPUTTE_ENUMS,
                'pagination' => null,
                'roundNames' => null
            ];
        }
        
        $dataService = DataServiceFactory::create($eventType);
        $matchesUpperCount = intval($event->tier?->tierTeamSlot);
        if (! $matchesUpperCount) {
            $previousValues = [];
        } else {
            $prevValues = $dataService->getPrevValues();
            $previousValues = $prevValues[$matchesUpperCount] ?? [];
        }

        $cacheKey = "{$eventType}_{$event->id}_{$matchesUpperCount}_{$page}_{$deadlinesHash}";
        
        $cachedData = Cache::remember($cacheKey, config('cache.ttl', 3600), function () use (
            $dataService, $matchesUpperCount, $willFixBracketsAsOrganizer, $USER_ENUMS, $deadlines, $page
        ) {
            $bracketList = $dataService->produceBrackets(
                $matchesUpperCount,
                $willFixBracketsAsOrganizer,
                $USER_ENUMS,
                $deadlines,
                $page
            );
            
            return [
                'bracketList' => $bracketList,
                'pagination' => $dataService->getPagination(),
                'roundNames' => $dataService->getRoundNames()
            ];
        });
        
        // Handle legacy cache data or ensure array structure
        if (is_array($cachedData) && isset($cachedData['bracketList'])) {
            $bracketList = $cachedData['bracketList'];
            $pagination = $cachedData['pagination'] ?? null;
            $roundNames = $cachedData['roundNames'] ?? null;
        } else {
            // Legacy cache format or direct bracket list
            $bracketList = is_array($cachedData) ? $cachedData : [];
            $pagination = null;
            $roundNames = null;
        }

      
        // Get join event IDs and team IDs efficiently
        $joinEventIds = $event->joinEvents->pluck('id');
        $teamIds = $event->joinEvents->pluck('team_id');

        // Cache roster data for better performance using event ID
        $rosterCacheKey = "roster_data_event_{$event->id}";
        $rosterMembers = Cache::remember($rosterCacheKey, config('cache.ttl', 3600), function () use ($teamIds, $joinEventIds) {
            return RosterMember::whereIn('team_id', $teamIds)
                ->whereIn('join_events_id', $joinEventIds)
                ->with(['user:id,name,userBanner'])
                ->select('id', 'team_id', 'join_events_id', 'user_id')
                ->get()
                ->groupBy('team_id');
        });

        // Load matches and teams efficiently
        $event->load([
            'joinEvents.team',
            'matches' => fn($query) => $query->whereIn('stage_name', $roundNames)
                ->with(['team1:id,teamName,teamBanner', 'team2:id,teamName,teamBanner'])
        ]);

        // Build team map and list efficiently using array access for better performance
        $teamMap = [];
        $teamList = collect();
        
        foreach ($event->joinEvents as $joinEvent) {
            $team = $joinEvent->team;
            $team->roster = $rosterMembers->get($team->id, collect());
            
            $teamMap[$team->id] = $team;
            if ($joinEvent->join_status === 'confirmed') {
                $teamList->push($team);
            }
        }
        
        $teamMap = collect($teamMap);


        $bracketList = $event->matches->reduce(function ($bracketList, $match, $index) use (
            $existingJoint,
            $willFixBracketsAsOrganizer,
            $USER_ENUMS,
            $rosterMembers,
        ) {
            $path = "{$match->stage_name}.{$match->inner_stage_name}.{$match->order}";
            $user_level = null;
            if ($existingJoint) {
                if ($match->team1_id === $existingJoint->team_id) {
                    $user_level = $USER_ENUMS['IS_TEAM1'];
                } elseif ($match->team2_id === $existingJoint->team_id) {
                    $user_level = $USER_ENUMS['IS_TEAM2'];
                } else {
                    $user_level = $USER_ENUMS['IS_PUBLIC'];
                }
            } else {
                $user_level = $willFixBracketsAsOrganizer ? $USER_ENUMS['IS_ORGANIZER'] : $USER_ENUMS['IS_PUBLIC'];
            }

            $existingData = data_get($bracketList, $path, []);

            // Pre-compute team data for efficiency
            $team1 = $match->team1;
            $team2 = $match->team2;
            
            $updatedProperties = [
                'id' => $match->id,
                'event_details_id' => $match->event_details_id,
                'team1_id' => $match->team1_id,
                'team2_id' => $match->team2_id,
                'team1_teamBanner' => $team1?->teamBanner,
                'team2_teamBanner' => $team2?->teamBanner,
                'team1_teamName' => $team1?->teamName,
                'team2_teamName' => $team2?->teamName,
                'team1_roster' => $match->team1_id ? $rosterMembers->get($match->team1_id, collect()) : collect(),
                'team2_roster' => $match->team2_id ? $rosterMembers->get($match->team2_id, collect()) : collect(),
                'team1_position' => $match->team1_position,
                'team2_position' => $match->team2_position,
                'team1_name' => $team1?->teamName,
                'team2_name' => $team2?->teamName,
                'user_level' => $user_level,
            ];

            $mergedData = array_merge($existingData, $updatedProperties);

            return data_set($bracketList, $path, $mergedData);
        }, $bracketList);

        // dd($bracketList, $event->matches, $roundNames);

        return [
            'teamList' => $teamList,
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList,
            'existingJoint' => $existingJoint,
            'previousValues' => $previousValues,
            'DISPUTE_ACCESS' => $DISPUTTE_ENUMS,
            'pagination' => $pagination,
            'roundNames' => $roundNames
        ];
    }

    public function getEventViewData(?EventDetail $event, ?User $user, ?JoinEvent $existingJoint): array
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
            'maxRosterSize' => $event->game?->player_per_team ?? config('constants.ROSTER_SIZE'),
        ];

        if ($user) {
            $viewData['user']->isFollowing = OrganizerFollow::isFollowing($userId, $event->user_id);
            $viewData['user']->isLiking = Like::isLiking($userId, $event->id);
        }

        return $viewData;
    }
}
