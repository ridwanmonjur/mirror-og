<?php

namespace Tests\Unit\Tournament;

use Tests\TestCase;
use Tests\Traits\CreatesTestEvents;
use App\Models\{BracketDeadline, EventDetail, Task};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;

class BracketDeadlineModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestEvents;

    /** @test */
    public function it_belongs_to_event_details()
    {
        $event = $this->createEvent();

        $deadline = BracketDeadline::factory()->create([
            'event_details_id' => $event->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $deadline->eventDetails);
        $this->assertEquals($event->id, $deadline->eventDetails->id);
    }

    /** @test */
    public function it_has_tasks_polymorphic_relationship()
    {
        $event = $this->createEvent();
        $deadline = BracketDeadline::factory()->create([
            'event_details_id' => $event->id,
        ]);

        Task::factory()->create([
            'taskable_id' => $deadline->id,
            'taskable_type' => BracketDeadline::class,
        ]);

        $deadline->refresh();
        $this->assertCount(1, $deadline->tasks);
        $this->assertInstanceOf(Task::class, $deadline->tasks->first());
    }

    /** @test */
    public function it_stores_stage_and_inner_stage()
    {
        $event = $this->createEvent();

        $deadline = BracketDeadline::factory()->create([
            'event_details_id' => $event->id,
            'stage' => 'Finals',
            'inner_stage' => 'Grand Final',
        ]);

        $this->assertEquals('Finals', $deadline->stage);
        $this->assertEquals('Grand Final', $deadline->inner_stage);
    }

    /** @test */
    public function it_stores_start_and_end_dates()
    {
        $event = $this->createEvent();
        $startDate = now()->addDays(5);
        $endDate = now()->addDays(10);

        $deadline = BracketDeadline::factory()->create([
            'event_details_id' => $event->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->assertEquals($startDate->format('Y-m-d H:i:s'), $deadline->start_date);
        $this->assertEquals($endDate->format('Y-m-d H:i:s'), $deadline->end_date);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $deadline = new BracketDeadline();
        $this->assertFalse($deadline->timestamps);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $event = $this->createEvent();

        $deadline = BracketDeadline::factory()->create([
            'event_details_id' => $event->id,
            'stage' => 'Semi Finals',
            'inner_stage' => 'Match 1',
            'start_date' => now(),
            'end_date' => now()->addHours(2),
        ]);

        $this->assertEquals($event->id, $deadline->event_details_id);
        $this->assertEquals('Semi Finals', $deadline->stage);
        $this->assertEquals('Match 1', $deadline->inner_stage);
    }

    /** @test */
    public function it_clears_cache_on_save()
    {
        $event = $this->createEvent();

        Cache::shouldReceive('forget')
            ->once()
            ->with("deadlines_event{$event->id}");

        $deadline = BracketDeadline::factory()->create([
            'event_details_id' => $event->id,
        ]);
    }
}
