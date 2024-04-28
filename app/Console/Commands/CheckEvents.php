<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'events:check';
    protected $description = 'Check events in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        $launchEvents = EventDetail::whereDate('launch_date', $today->toDateString())
            ->whereTime('launch_time', '<=', $today->toTimeString())
            ->get();

        $endEvents = EventDetail::whereDate('end_date', $today->toDateString())->get();

        $twoWeeksAgo = $today->subWeeks(2);
        $elapsedLaunchEvents = EventDetail::where('launch_date', '<=', $twoWeeksAgo)
            ->get();
    }
}
