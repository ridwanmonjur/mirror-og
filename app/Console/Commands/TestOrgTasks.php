<?php

namespace App\Console\Commands;

use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\Task;
use App\Services\BracketDataService;
use Carbon\Carbon;
use Illuminate\Console\Command;


class TestOrgTasks extends Command
{
    use DeadlineTasksTrait, PrinterLoggerTrait;

    protected $signature = 'tasks:org {taskable_id?}';
    protected $description = 'Test only the organization report tasks';

      
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
            $bracketInfoMap = [];
            $eventDetails = EventDetail::whereIn('id', [$taskableId])
                ->with(['eventTier', 'matches'])
                ->get();
            $bracketDeadlines = BracketDeadline::whereIn('event_details_id', [$taskableId])->get();

            foreach ($eventDetails as $detail) {
                if (!isset($bracketInfoMap[$detail->id]) && isset($detail->eventTier?->tierTeamSlot)) {
                    $bracketInfoMap[$detail->id] = $this->bracketDataService->produceBrackets($detail->eventTier->tierTeamSlot, false, null, null);
                }
            }
            
            $this->handleOrgTasks($bracketDeadlines, $bracketInfoMap);
            
            $this->logExit($taskId, Carbon::now());
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->logError($taskId, $e);
            return 1;
        }
    }
}