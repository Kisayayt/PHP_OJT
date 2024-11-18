<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Configuration;
use App\Mail\ReminderCheckinCheckout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReminderEmails extends Command
{
    protected $signature = 'emails:send-reminders';
    protected $description = 'Gửi email nhắc nhở check-in/check-out cho nhân viên nếu đã qua thời gian reminder';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentTime = now()->format('H:i:s');

        // Lấy cấu hình thời gian reminder
        $configuration = Configuration::where('name', 'reminder')->first();

        if (!$configuration) {
            Log::warning("No reminder time found in configurations.");
            return;
        }

        $reminderTime = $configuration->time;

        // Chỉ gửi email nếu thời gian hiện tại >= thời gian reminder
        if ($currentTime < $reminderTime) {
            Log::info("Current time ({$currentTime}) has not reached reminder time ({$reminderTime}). No emails sent.");
            return;
        }

        // Lấy tất cả nhân viên
        $users = User::all();

        Log::info("Start sending reminders to users...");

        foreach ($users as $user) {
            Log::info("Sending reminder to {$user->email}");
            Mail::to($user->email)->send(new ReminderCheckinCheckout($user));
        }

        $this->info('Đã gửi email nhắc nhở cho tất cả nhân viên.');
        Log::info("All reminder emails sent.");
    }
}
