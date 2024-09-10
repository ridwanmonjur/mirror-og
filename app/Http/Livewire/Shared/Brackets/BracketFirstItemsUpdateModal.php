<?php

namespace App\Http\Livewire\Shared\Brackets;

use Livewire\Component;

class BracketFirstItemsUpdateModal extends Component
{

    public $event;
    public $teamList;

    public function mount($event, $teamList)
    {
        $this->event = $event;
        $this->teamList = $teamList;
    }

    public function render()
    {
        return view('livewire.shared.brackets.bracket-update-modal');
    }
}
