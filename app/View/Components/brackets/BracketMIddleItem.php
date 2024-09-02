<?php

namespace App\View\Components\brackets;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BracketMIddleItem extends Component
{
    public $bracket = null;

    public function __construct($bracket) {
        $this->bracket = $bracket;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.brackets.bracket-middle-item');
    }
}
