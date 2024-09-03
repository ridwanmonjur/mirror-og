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
        $bracketList = $bracketData->getData($matchesUpperCount)[$valuesMap[$tournamentType]];
        // $this->event->matches->each(function ($match) use (&$bracketList) {
        
        //     switch ($match->type) {
        //         case 'finals':
        //             $bracketList['doubleElimination']['finals']['finals'][] = $match;
        //             break;
        //         case 'eliminator1':
        //             $bracketList['doubleElimination']['upperBracket']['eliminator1'][] = $match;
        //             break;
        //     }
        // });
        
        // if (empty($matchesArray['doubleElimination']['finals']['finals'])) {
        //     $bracketList['doubleElimination']['finals']['finals'][] = [
        //         'team1Position' => 'G1',
        //         'team2Position' => 'G2',
        //         'order' => 1,
        //         'winnerNext' => null,
        //         'loserNext' => null,
        //     ];
        // }
        
        // if (empty($matchesArray['doubleElimination']['upperBracket']['eliminator1'])) {
        //     $bracketList['doubleElimination']['upperBracket']['eliminator1'][] = [
        //         'team1Position' => '',
        //         'team2Position' => '',
        //         'order' => 1,
        //         'winnerNext' => 'U1',
        //         'loserNext' => 'L1',
        //     ];
        // }
        return view('livewire.shared.brackets.bracket-update-list', [
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList
        ]);
    }
}
