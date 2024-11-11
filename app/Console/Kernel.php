<?php

namespace App\Console;

use App\Http\Controllers\CheckInOutNotificationController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('email:send-attendance-reminder')->dailyAt(env('CHECKIN_TIME', '08:00'));
        $schedule->command('email:send-attendance-reminder')->dailyAt(env('CHECKOUT_TIME', '17:00'));
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
