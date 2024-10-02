<?php

namespace App\Http\Livewire\Shared\Brackets;

use App\Models\BracketData;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Team;
use App\Models\Matches;
use App\Models\RosterMember;
use Livewire\Component;

class BracketReportList extends Component
{
    public $eventId = null;
    public $event = null;
    public $teamList;
    public $rosterList = null;
    public $isShow = true;


    public function __construct(
            string | int  $eventId,
        ) {
            $this->eventId = $eventId;
        }

    public function mount() {
        $this->event = EventDetail::with(['type'])->findOrFail($this->eventId);

        // if () {
        //     $this->isShow = true;
        //     return;
        // }

        $this->event->load([
            'tier',
            'type',
            'joinEvents.team' => function ($query) {
                $query->select('teams.id', 'teamName', 'teamBanner');
            },
        ]);
        
        $joinEventIds = $this->event->joinEvents->pluck('id');
        
        $this->event->load([
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
        $this->event->joinEvents->each(function ($joinEvent) use (&$teamList, &$teamMap) {
            $teamMap[$joinEvent->team->id] = $joinEvent->team;
            $teamList->push($joinEvent->team);
        });

        $matchTeamIds = collect();
        $this->event->matches->each(function ($match) use ($teamMap, &$matchTeamIds) {
            $match->team1 = $teamMap->get($match->team1_id);
            $match->team2 = $teamMap->get($match->team2_id);
            $matchTeamIds->push($match->team1_id, $match->team2_id);
        });

        $this->teamList = $teamList;
    }

    public function render()
    {
        // if ($this->isShow) {
        //     return '<div>Hi</div>';
        // }

        $defaultValues = BracketData::DEFAULT_VALUES;
        $matchesUpperCount = intval($this->event->tier->tierTeamSlot); 
        $valuesMap = ['Tournament' => 'tournament', 'League' => 'tournament'];
        $tournamentType = $this->event->type->eventType;
        $tournamentTypeFinal = $valuesMap[$tournamentType];

        $bracketList = BracketData::BRACKET_DATA[(int)($matchesUpperCount)][$tournamentTypeFinal];
        $previousValues = BracketData::PREV_VALUES[(int)($matchesUpperCount)];

        $bracketList = $this->event->matches->reduce(function ($bracketList, $match) {
            $path = "{$match->stage_name}.{$match->inner_stage_name}.{$match->order}";

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
                'team1_score' => $match->team1_score,
                'team2_score' => $match->team2_score,
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
            ]);
        }, $bracketList);
        
        
        if (empty($matchesArray['tournament']['finals']['finals'])) {
            $bracketList['tournament']['finals']['finals'][] = [
                'team1_position' => 'G1',
                'team2_position' => 'G2',
                'order' => 1,
                'winner_next_position' => null,
                'loser_next_position' => null,
            ];
        }
        
        if (empty($matchesArray['tournament']['upperBracket']['eliminator1'])) {
            $bracketList['tournament']['upperBracket']['eliminator1'][] = [
                'team1_position' => '',
                'team2_position' => '',
                'order' => 1,
                'winner_next_position' => 'U1',
                'loser_next_position' => 'L1',
            ];
        }

        return view('livewire.shared.brackets.bracket-report-list', [
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList,
            'defaultValues' => $defaultValues,
            'previousValues' => $previousValues
        ]);
    }
}
