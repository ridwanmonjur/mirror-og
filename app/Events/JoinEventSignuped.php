<?php

namespace App\Events;

use App\Models\EventDetail;
use App\Models\Team;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JoinEventSignuped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public $join_id;

    public Team $selectTeam;

    public EventDetail $event;


    /**
     * Create a new event instance.
     */
    public function __construct($parameters)
    {
        $this->user ??= $parameters['user'];
        $this->selectTeam ??= $parameters['selectTeam'];
        $this->event ??= $parameters['event'];
        $this->join_id ??= $parameters['join_id'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
