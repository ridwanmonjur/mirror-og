<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use ReflectionObject;
use ReflectionProperty;

class JoinEventConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $memberNotification; 
    public $organizerNotificatio; 
    public $memberList; 
    public $organizerList;
    /**
     * Create a new event instance.
     */
    public function __construct($memberList, $organizerList, $memberNotification, $organizerNotificatio)
    {
        $this->memberNotification = $memberNotification;
        $this->organizerNotificatio = $organizerNotificatio;
        $this->memberList = $memberList;
        $this->organizerList = $organizerList;
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

    public function __toString()
    {
        $string = $this->getModelAsString();
        return $string;
    }

    protected function getModelAsString()
    {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        $string = get_class($this) . " {";
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($this);
            $stringValue = is_object($propertyValue) ? $propertyValue->__toString() : var_export($propertyValue, true);
            $string .= "$propertyName: $stringValue, ";
        }
        $string = rtrim($string, ', ') . "}";
        return $string;
    }
}
