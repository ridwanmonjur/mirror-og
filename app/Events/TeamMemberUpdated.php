<?php

namespace App\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class TeamMemberUpdated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = config('constants.QUEUE_TRIES'); 

    public $teamMember;

    public function __construct($teamMember)
    {
        $this->teamMember = $teamMember;
    }
}
