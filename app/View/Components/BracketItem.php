<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BracketItem extends Component
{
    public $team1;
    public $team1Code;
    public $team1Score;
    public $team2;
    public $team2Code;
    public $team2Score;
    public $date;

    // Constructor to initialize the properties
    public function __construct($team1 = 'Team 1', $team1Code = 'T1', $team1Score = 0, $team2 = 'Team 2', $team2Code = 'T2', $team2Score = 0, $date = '1970-01-01')
    {
        $this->team1 = $team1;
        $this->team1Code = $team1Code;
        $this->team1Score = $team1Score;
        $this->team2 = $team2;
        $this->team2Code = $team2Code;
        $this->team2Score = $team2Score;
        $this->date = $date;
    }

    public function render()
    {
        return view('components.bracket-item');
    }
}
