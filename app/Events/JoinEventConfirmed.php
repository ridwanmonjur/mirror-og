<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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

    public $allEventLogs;

    /**
     * Create a new event instance.
     */
    public function __construct($parameters)
    {
        $this->memberList ??= $parameters['memberList'];
        $this->organizerList ??= $parameters['organizerList'];
        $this->memberNotification ??= $parameters['memberNotification'];
        $this->organizerNotificatio ??= $parameters['organizerNotification'];
        $this->allEventLogs ??= $parameters['allEventLogs'];
    }

    public function __toString()
    {
        return $this->getModelAsString();
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

    protected function getModelAsString()
    {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        $string = get_class($this).' {';
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($this);
            $stringValue = is_object($propertyValue) ? $propertyValue->__toString() : var_export($propertyValue, true);
            $string .= "{$propertyName}: {$stringValue}, ";
        }
        $string = rtrim($string, ', ').'}';

        return $string;
    }
}
