<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JoinEventSignuped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $memberNotification;

    public $organizerNotification;

    public $memberList;

    public $organizerList;

    public $allEventLogs;

    /**
     * Create a new event instance.
     */
    public function __construct($parameters)
    {
        $this->memberList ??= $parameters['memberList'];
        $this->organizerList ??= $parameters['organizerList'];
        $this->memberNotification ??= $parameters['memberNotification'];
        $this->organizerNotification ??= $parameters['organizerNotification'];
        $this->allEventLogs ??= $parameters['allEventLogs'];
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
