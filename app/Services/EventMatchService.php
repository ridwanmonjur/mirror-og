<?php
namespace App\Services;

use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class EventMatchService {

    private $bracketDataService;

    public function __construct(BracketDataService $bracketDataService)
    {
        $this->bracketDataService = $bracketDataService;
    }

    public function generateBrackets(EventDetail $event, 
        bool $isOrganizer, 
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
        $event->joinEvents->each(function ($joinEvent) use (&$teamList, &$teamMap) {
            $teamMap[$joinEvent->team->id] = $joinEvent->team;
            $teamList->push($joinEvent->team);
        });

        $matchTeamIds = collect();
        $event->matches->each(function ($match) use ($teamMap, &$matchTeamIds) {
            $match->team1 = $teamMap->get($match->team1_id);
            $match->team2 = $teamMap->get($match->team2_id);
            $matchTeamIds->push($match->team1_id, $match->team2_id);
        });

        $matchesUpperCount = intval($event->tier?->tierTeamSlot); 
        $previousValues = $this->bracketDataService::PREV_VALUES[$matchesUpperCount];
        $valuesMap = ['Tournament' => 'tournament', 'League' => 'tournament'];
        $tournamentType = $event->type?->eventType;
        $tournamentTypeFinal = $valuesMap[$tournamentType];
        $bracketList = $this->bracketDataService->produceBrackets(
            $matchesUpperCount, 
            $isOrganizer
        )[$tournamentTypeFinal];
        $bracketList = $event->matches->reduce(function ($bracketList, $match) use (
                $existingJoint, 
                $isOrganizer,
                $USER_ENUMS,
            ) {
            $path = "{$match->stage_name}.{$match->inner_stage_name}.{$match->order}";
            $user_level = $isOrganizer ? $USER_ENUMS['IS_ORGANIZER'] : null;
            if ($match->team1_id === $existingJoint?->team_id) $user_level = $USER_ENUMS['IS_TEAM1'];
            elseif ($match->team2_id === $existingJoint?->team_id) $user_level = $USER_ENUMS['IS_TEAM2'];
            elseif ($user_level === null) $user_level = $USER_ENUMS['IS_PUBLIC'];
            $match->user_level = $user_level;
            $match->will_json = $user_level !== $USER_ENUMS['IS_PUBLIC'];
            return data_set($bracketList, $path, [
                'id' => $match->id,
                'event_details_id' => $match->event_details_id,
                'match_type' => $match->match_type,
                'stage_name' => $match->stage_name,
                'inner_stage_name' => $match->inner_stage_name,
                'order' => $match->order,
                'team1_id' => $match->team1_id,
                'team2_id' => $match->team2_id,
                'team1_teamBanner' => $match->team1->teamBanner,
                'team2_teamBanner' => $match->team2->teamBanner,
                'team1_teamName' => $match->team1->teamName,
                'team2_teamName' => $match->team2->teamName,
                'team1_roster' => $match->team1->roster,
                'team2_roster' => $match->team2->roster,
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
                'will_json' => $match->will_json,
                'user_level'  => $match->user_level,
            ]);

           
        }, $bracketList);
        
        if (empty($bracketList['tournament']['finals']['finals'])) {
            $bracketList['tournament']['finals']['finals'][] = [
                'team1_position' => 'G1',
                'team2_position' => 'G2',
                'order' => 1,
                'winner_next_position' => null,
                'loser_next_position' => null,
            ];
        }
        
        if (empty($bracketList['tournament']['upperBracket']['eliminator1'])) {
            $bracketList['tournament']['upperBracket']['eliminator1'][] = [
                'team1_position' => '',
                'team2_position' => '',
                'order' => 1,
                'winner_next_position' => 'U1',
                'loser_next_position' => 'L1',
            ];
        }

        return [
            'teamList' => $teamList,
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList,
            'existingJoint' => $existingJoint,
            'previousValues' => $previousValues
        ];
    }

}