<?php

namespace App\Http\Livewire\Shared\Brackets;

use App\Http\Livewire\Shared\data\BracketData;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Team;
use App\Models\Matches;

use Livewire\Component;

class BracketUpdateList extends Component
{
    public $id = null;
    public $event = null;
    public $teamList;

    public function __construct(string | int | null $id = null) {
        $this->id = $id;
    }

    public function mount() {
        $this->event = EventDetail::with(['tier', 'type', 
                'joinEvents', 'matches', 'matches.team1', 'matches.team2'
            ]) 
            ->select(['id', 'eventBanner', 'eventName', 'eventDescription', 'event_tier_id', 'event_type_id'])
            ->findOrFail($this->id);

        $teamIds = $this->event->joinEvents->pluck('team_id');
        $this->teamList = Team::whereIn('id', $teamIds)->get();
    }

    public function render()
    {
        $matchesUpperCount = intval($this->event->tier->tierTeamSlot); 
        $valuesMap = ['Tournament' => 'doubleElimination', 'League' => 'doubleElimination'];
        $tournamentType = $this->event->type->eventType;
        $bracketData = new BracketData;
        $tournamentTypeFinal = $valuesMap[$tournamentType];
        $bracketList = $bracketData->getData($matchesUpperCount)[$tournamentTypeFinal];
        $this->event->matches->each(function ($match) use (&$bracketList) {
            $bracketList[$match->tournamentTypeFinal][$match->stage_name][$match->inner_stage_name][$match->order]['team1_position'] = 
                $match->team1_position ?? null;
            $bracketList[$match->tournamentTypeFinal][$match->stage_name][$match->inner_stage_name][$match->order]['team2_position'] = 
                $match->team2_position ?? null;
            $bracketList[$match->tournamentTypeFinal][$match->stage_name][$match->inner_stage_name][$match->order]['team1_id'] = 
                $match->team1_position ?? null;
            $bracketList[$match->tournamentTypeFinal][$match->stage_name][$match->inner_stage_name][$match->order]['team2_id'] = 
                $match->team2_position ?? null;
        });
        
        if (empty($matchesArray['doubleElimination']['finals']['finals'])) {
            $bracketList['doubleElimination']['finals']['finals'][] = [
                'team1_position' => 'G1',
                'team2_position' => 'G2',
                'order' => 1,
                'winner_next_position' => null,
                'loser_next_position' => null,
            ];
        }
        
        if (empty($matchesArray['doubleElimination']['upperBracket']['eliminator1'])) {
            $bracketList['doubleElimination']['upperBracket']['eliminator1'][] = [
                'team1_position' => '',
                'team2_position' => '',
                'order' => 1,
                'winner_next_position' => 'U1',
                'loser_next_position' => 'L1',
            ];
        }
        return view('livewire.shared.brackets.bracket-update-list', [
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList
        ]);
    }
}
