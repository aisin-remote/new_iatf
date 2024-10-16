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
        $schedule->command('command:send-documentobsolete-reminder')
            ->daily() // Setiap hari
            ->timezone('Asia/Jakarta')
            ->at('07.00'); // Waktu pengingat
        $schedule->command('command:send-reviewdocument-reminder')
            ->daily() // Setiap hari
            ->timezone('Asia/Jakarta')
            ->at('07:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
