<?php

namespace App\Console;

use App\Console\Commands\SendReminderEmails;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SendReminderEmails::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Đặt lịch cho lệnh gửi email vào thời gian định kỳ
        $schedule->command('emails:send-reminders')->dailyAt('08:00');

        // Log để kiểm tra lịch trình chạy
        $schedule->call(function () {
            Log::info('Schedule is running at ' . now());
        })->everyMinute();  // Kiểm tra cron job chạy mỗi phút
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
