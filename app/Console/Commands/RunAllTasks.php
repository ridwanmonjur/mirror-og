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
    protected $signature = 'tasks:run-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all tasks: deadline, respond, and weekly tasks';

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
        $this->info('Starting all tasks execution...');

        try {
            $this->deadlineTaskService->execute();

            $this->respondTaskService->execute();

            if ($this->shouldRunWeeklyTasks()) {
                $this->runWeeklyTasks();
            } 

            $this->info('All tasks completed successfully.');
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
}
