<?php

namespace App\View\Components\brackets;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BracketWinnerItem extends Component
{
    public $bracket = null;

    public $stageName = null;

    public $innerStageName = null;

    public $order = null;

    public $winner = null;

    public function __construct($bracket, $stageName, $innerStageName, $order, $winner = null)
    {
        $this->bracket = $bracket;
        $this->stageName = $stageName;
        $this->innerStageName = $innerStageName;
        $this->order = $order;
        $this->winner = $winner;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.brackets.bracket-winner-item');
    }
}
