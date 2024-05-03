<?php
namespace App\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMemberUpdated implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $teamMember;

    public function __construct($teamMember)
    {
        $this->teamMember = $teamMember;
    }
}
