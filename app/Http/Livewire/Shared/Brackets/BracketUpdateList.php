<?php

namespace App\Http\Livewire\Shared\Brackets;

use App\Http\Livewire\Shared\data\BracketData;
use App\Models\EventDetail;
use Livewire\Component;

class BracketUpdateList extends Component
{
    public $id = null;
    public $event = null;

    public function __construct(string | int | null $id = null) {
        $this->id = $id;
    }

    public function mount() {
        $this->event = EventDetail::with(['tier', 'type'])
            ->select(['id', 'eventBanner', 'eventName', 'eventDescription', 'event_tier_id', 'event_type_id'])
            ->findOrFail($this->id);
    }

    public function render()
    {
        $matchesUpperCount = intval($this->event->tier->tierTeamSlot); 
        $valuesMap = ['Tournament ' => 'doubleElimination', 'League' => 'doubleElimination'];
        $tournamentType = $this->event->type->eventType;
        $bracketData = new BracketData;
        $bracketList = $bracketData->getData($matchesUpperCount)[$valuesMap[$tournamentType]];

        return view('livewire.shared.brackets.bracket-update-list', [
            'matchesUpperCount' => $matchesUpperCount,
            'bracketList' => $bracketList
        ]);
    }
}
