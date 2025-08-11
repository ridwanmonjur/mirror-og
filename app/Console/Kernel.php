<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('queue:work --stop-when-empty')
        //     ->everyMinute()
        //     ->withoutOverlapping();

        // $schedule->command('queue:restart')->hourly();

        $schedule->command('tasks:respond')->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('tasks:weekly')->weeklyOn(1, '00:00')->withoutOverlapping(); // Monday
        $schedule->command('tasks:weekly')->weeklyOn(4, '00:00')->withoutOverlapping(); // Thursday
        $schedule->command('tasks:monthly')->monthlyOn(1, '00:00')->withoutOverlapping(); // Thursday

        // */5 * * * * /usr/bin/php /home/u472033366/domains/oceansgaming.gg/public_html/artisan schedule:run >> /dev/null 2>&1
        // */5 * * * * /usr/bin/php /home/u472033366/domains/driftwood.gg/public_html/artisan schedule:run >> /dev/null 2>&1

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
