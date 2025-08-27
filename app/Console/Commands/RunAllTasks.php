<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\DeadlineTaskService;
use App\Services\RespondTaskService;
use App\Services\WeeklyTaskService;
use Carbon\Carbon;

class RunAllTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:run-all {task_type=0 : Task type to run: 0=all, 1-5=respond, 6-8=deadline, 9=weekly} {--event_id= : Optional event ID to filter tasks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run specific tasks based on task_type: 0=all, 1-5=respond, 6-8=deadline, 9=weekly';

    protected $deadlineTaskService;
    protected $respondTaskService;
    protected $weeklyTaskService;

    public function __construct(
        DeadlineTaskService $deadlineTaskService,
        RespondTaskService $respondTaskService,
        WeeklyTaskService $weeklyTaskService
    ) {
        parent::__construct();
        $this->deadlineTaskService = $deadlineTaskService;
        $this->respondTaskService = $respondTaskService;
        $this->weeklyTaskService = $weeklyTaskService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $taskType = (int) $this->argument('task_type');
        $eventId = $this->option('event_id');
        
        $this->info("Starting task execution (type: {$taskType})" . 
                   ($eventId ? " for event ID: {$eventId}" : ""));

        try {
            $this->runSpecificTask($taskType, $eventId);
            $this->info('Task execution completed successfully.');
        } catch (\Exception $e) {
            $this->error('Task execution failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function shouldRunWeeklyTasks(): bool
    {
        $today = Carbon::today();
        
        if ($today->dayOfWeek != Carbon::MONDAY) {
            return false;
        }

        $weekStart = $today->startOfWeek();
        $weekEnd = $today->endOfWeek();
        
        return !DB::table('weekly_task_executions')
            ->whereBetween('executed_at', [$weekStart, $weekEnd])
            ->exists();
    }

    private function runWeeklyTasks(): void
    {
        $this->info('Running weekly tasks...');
        $this->weeklyTaskService->execute();
        
        DB::table('weekly_task_executions')->insert([
            'executed_at' => now(),
        ]);
        
        $this->info('Weekly tasks completed successfully.');
    }

    /**
     * Run specific task based on task type
     */
    private function runSpecificTask(int $taskType, ?string $eventId): void
    {
        switch ($taskType) {
            case 0: // tasks_all - run all services
                $this->runAllTasks($eventId);
                break;
            case 1: // event_started - RespondTaskService
            case 2: // event_live - RespondTaskService
            case 3: // event_ended - RespondTaskService
            case 4: // event_reg_over - RespondTaskService
            case 5: // event_resetStart - RespondTaskService
                $this->info("Running respond task (type: {$taskType})...");
                $this->respondTaskService->execute($taskType, $eventId, $taskType);
                break;
            case 6: // report_start - DeadlineTaskService
            case 7: // report_end - DeadlineTaskService
            case 8: // report_org - DeadlineTaskService
                $this->info("Running deadline task (type: {$taskType})...");
                $this->deadlineTaskService->execute(0, $eventId, $taskType);
                break;
            case 9: // weekly_tasks - WeeklyTaskService
                $this->info("Running weekly task (type: {$taskType})...");
                $this->weeklyTaskService->execute($taskType);
                break;
            default:
                throw new \InvalidArgumentException("Invalid task type: {$taskType}");
        }
    }

    /**
     * Run all tasks (deadline, respond, and weekly if conditions are met)
     */
    private function runAllTasks(?string $eventId): void
    {
        if ($eventId) {
            $this->info('Running deadline task for event...');
            $this->deadlineTaskService->execute(0, $eventId);

            $this->info('Running respond task for event...');
            $this->respondTaskService->execute(0, $eventId);
        } else {
            $this->info('Running deadline task...');
            $this->deadlineTaskService->execute();

            $this->info('Running respond task...');
            $this->respondTaskService->execute();

            if ($this->shouldRunWeeklyTasks()) {
                $this->runWeeklyTasks();
            }
        }
    }
}
