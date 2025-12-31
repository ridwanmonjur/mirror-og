<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\{Task, BracketDeadline, EventDetail};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaskModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $task = Task::create([
            'task_name' => 'Update Bracket',
            'action_time' => now(),
            'taskable_type' => BracketDeadline::class,
            'taskable_id' => 1,
            'event_id' => 1,
        ]);

        $this->assertEquals('Update Bracket', $task->task_name);
        $this->assertNotNull($task->action_time);
        $this->assertEquals(BracketDeadline::class, $task->taskable_type);
        $this->assertEquals(1, $task->taskable_id);
        $this->assertEquals(1, $task->event_id);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $task = new Task();

        $this->assertFalse($task->timestamps);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $task = new Task();

        $this->assertEquals('tasks', $task->getTable());
    }

    /** @test */
    public function it_has_morph_to_relationship()
    {
        $event = EventDetail::factory()->create();

        $deadline = BracketDeadline::create([
            'event_details_id' => $event->id,
            'stage' => 1,
            'inner_stage' => 1,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
        ]);

        $task = Task::create([
            'task_name' => 'Advance Stage',
            'action_time' => now(),
            'taskable_type' => BracketDeadline::class,
            'taskable_id' => $deadline->id,
            'event_id' => $event->id,
        ]);

        $this->assertEquals(BracketDeadline::class, $task->taskable_type);
        $this->assertEquals($deadline->id, $task->taskable_id);
    }

    /** @test */
    public function it_can_create_task_for_event()
    {
        $task = Task::create([
            'task_name' => 'Send Notifications',
            'action_time' => now()->addHours(2),
            'taskable_type' => 'App\Models\EventDetail',
            'taskable_id' => 1,
            'event_id' => 1,
        ]);

        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Send Notifications',
            'event_id' => 1,
        ]);
    }
}
