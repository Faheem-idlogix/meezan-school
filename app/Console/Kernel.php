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
        // Daily database backup at 2:00 AM — email zip to configured address
        $schedule->command('db:backup --keep=7')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->onOneServer()
                 ->appendOutputTo(storage_path('logs/backup.log'));

        // Weekly full backup on Sundays at 3:00 AM with longer retention
        $schedule->command('db:backup --keep=30')
                 ->weeklyOn(0, '03:00')
                 ->withoutOverlapping()
                 ->onOneServer()
                 ->appendOutputTo(storage_path('logs/backup.log'));

        // Clean old activity logs older than 180 days (monthly)
        $schedule->call(function () {
            \App\Models\ActivityLog::where('created_at', '<', now()->subDays(180))->delete();
        })->monthly()->appendOutputTo(storage_path('logs/cleanup.log'));
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
