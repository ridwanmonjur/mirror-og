<?php

namespace App\Events;

use App\Models\TeamMember;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class TeamMemberCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3; 

    public $teamMember;

    public function __construct(TeamMember $teamMember)
    {
        $this->teamMember = $teamMember;
    }
}
