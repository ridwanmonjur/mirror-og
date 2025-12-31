<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\WeeklyTaskExecution;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WeeklyTaskExecutionModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $execution = WeeklyTaskExecution::create([
            'executed_at' => now(),
        ]);

        $this->assertNotNull($execution->executed_at);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $execution = new WeeklyTaskExecution();

        $this->assertFalse($execution->timestamps);
    }

    /** @test */
    public function it_casts_executed_at_to_datetime()
    {
        $execution = WeeklyTaskExecution::create([
            'executed_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $execution->executed_at);
    }

    /** @test */
    public function it_can_record_execution_time()
    {
        $time = now();

        $execution = WeeklyTaskExecution::create([
            'executed_at' => $time,
        ]);

        $this->assertTrue($execution->executed_at->isSameSecond($time));
    }

    /** @test */
    public function it_can_create_multiple_executions()
    {
        WeeklyTaskExecution::create([
            'executed_at' => now()->subWeek(),
        ]);

        WeeklyTaskExecution::create([
            'executed_at' => now(),
        ]);

        $this->assertEquals(2, WeeklyTaskExecution::count());
    }

    /** @test */
    public function it_can_query_recent_executions()
    {
        WeeklyTaskExecution::create([
            'executed_at' => now()->subWeeks(2),
        ]);

        WeeklyTaskExecution::create([
            'executed_at' => now()->subDays(3),
        ]);

        WeeklyTaskExecution::create([
            'executed_at' => now(),
        ]);

        $recent = WeeklyTaskExecution::where('executed_at', '>=', now()->subWeek())->get();

        $this->assertCount(2, $recent);
    }
}
