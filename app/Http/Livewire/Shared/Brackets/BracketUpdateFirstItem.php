<?php

namespace App\Http\Livewire\Shared\Brackets;

use Livewire\Component;

class BracketUpdateFirstItem extends Component
{
    public $bracket = null;

    public function __construct($bracket) {
        $this->bracket = $bracket;
    }

    // public function mount() {
      
    // }

    public function render()
    {
        return view('livewire.shared.brackets.bracket-update-first-item');
    }
}
