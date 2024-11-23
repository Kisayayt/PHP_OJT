<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Configuration;
use App\Mail\ReminderCheckinCheckout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $testCurrentTime = Carbon::createFromFormat('H:i:s', '8:30:00');
        $currentTime = Carbon::now();
        $configuration = Configuration::where('name', 'reminder')->first();

        if (!$configuration) {
            Log::warning("No reminder time found in configurations.");
            return;
        }

        $reminderTime = Carbon::createFromFormat('H:i:s', $configuration->time);

        Log::info("Reminder time from configuration: {$reminderTime->toTimeString()}");
        Log::info("Test time: {$testCurrentTime->toTimeString()}");
        Log::info("Current time: {$currentTime->toTimeString()}");

        // So sánh thời gian test
        if ($currentTime->lt($reminderTime)) {
            Log::info("Test time ({$currentTime->toTimeString()}) has not reached reminder time ({$reminderTime->toTimeString()}). No emails sent.");
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
