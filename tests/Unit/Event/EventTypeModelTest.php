<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use App\Models\EventType;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventTypeModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_stores_event_type()
    {
        $eventType = EventType::factory()->create([
            'eventType' => 'Single Elimination',
        ]);

        $this->assertEquals('Single Elimination', $eventType->eventType);
    }

    /** @test */
    public function it_stores_event_definitions()
    {
        $eventType = EventType::factory()->create([
            'eventDefinitions' => 'Best of 3',
        ]);

        $this->assertEquals('Best of 3', $eventType->eventDefinitions);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $eventType = new EventType();
        $this->assertNull($eventType->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $eventType = EventType::factory()->create([
            'eventType' => 'Double Elimination',
            'eventDefinitions' => 'Best of 5',
        ]);

        $this->assertEquals('Double Elimination', $eventType->eventType);
        $this->assertEquals('Best of 5', $eventType->eventDefinitions);
    }

    /** @test */
    public function it_can_update_event_type()
    {
        $eventType = EventType::factory()->create([
            'eventType' => 'Round Robin',
        ]);

        $eventType->update(['eventType' => 'Swiss Tournament']);

        $this->assertEquals('Swiss Tournament', $eventType->fresh()->eventType);
    }
}
