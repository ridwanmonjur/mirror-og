<?php

namespace App\Console\Commands;

use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\Task;
use App\Services\BracketDataService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestStartTasks extends Command
{
    use DeadlineTasksTrait, PrinterLoggerTrait;

    protected $signature = 'tasks:start {taskable_id?}';
    protected $description = 'Test only the start report tasks';

      
    public function __construct(BracketDataService $bracketDataService)
    {
        parent::__construct();
        
        $this->initializeDeadlineTasksTrait($bracketDataService);
    }

    
    public function handle()
    {
        $now = Carbon::now();
        $taskId = $this->logEntry($this->description, $this->signature, 'manual', $now);
        
        try {
            $taskableId = $this->argument('taskable_id');
            $bracketDeadlines = BracketDeadline::whereIn('event_details_id', [$taskableId])->get();
            $eventDetails = EventDetail::whereIn('id', [$taskableId])
                ->withEventTierAndFilteredMatches($bracketDeadlines)
                ->get();

            foreach ($eventDetails as $detail) {
                $this->handleStartedTasks($detail->matches);
            }
            
            $this->logExit($taskId, Carbon::now());
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->logError($taskId, $e);
            return 1;
        }
    }
}
