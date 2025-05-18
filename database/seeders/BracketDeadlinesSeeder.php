<?php

namespace Database\Seeders;

use App\Services\EventMatchService;
use Illuminate\Database\Seeder;
use App\Models\EventDetail;
use App\Models\BracketDeadlineSetup;
use App\Models\BracketDeadline;
use App\Models\Task;
use Carbon\Carbon;

class BracketDeadlinesSeeder extends Seeder
{
    private $eventMatchService;

    public function __construct(
        EventMatchService $eventMatchService
    )
    {
        $this->eventMatchService = $eventMatchService;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventDetails = EventDetail::whereNotNull('startDate')
            ->whereNotNull('startTime')
            ->whereNotNull('event_tier_id')
            ->whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
            ->get();
        
        foreach ($eventDetails as $detail) {
            $detail->createDeadlinesTask();
        }
        
        $this->command->info('Bracket deadlines have been created successfully!');
    }

}