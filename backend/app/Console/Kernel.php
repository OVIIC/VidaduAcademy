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
        // Check and fix pending payments every hour
        $schedule->command('payments:fix-pending --days=1')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();
            
        // Weekly cleanup of older pending payments
        $schedule->command('payments:fix-pending --days=7')
            ->weekly()
            ->sundays()
            ->at('02:00');
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
