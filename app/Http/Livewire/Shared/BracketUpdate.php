<?php

namespace App\Http\Livewire\Shared;

use App\Models\EventDetail;
use Livewire\Component;

class BracketUpdate extends Component
{
    public $id = null;
    public $event = null;

    public function __construct(string | int | null $id = null) {
        $this->id = $id;
    }

    public function mount() {
        $this->event = EventDetail::with(['tier'])
            ->select(['id', 'eventBanner', 'eventName', 'eventDescription', 'event_tier_id'])
            ->findOrFail($this->id);
    }

    public function render()
    {
        return view('livewire.shared.bracket-update');
    }
}
