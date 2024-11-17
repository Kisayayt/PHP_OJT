<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\ReminderCheckinCheckout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReminderEmails extends Command
{
    protected $signature = 'emails:send-reminders';
    protected $description = 'Gửi email nhắc nhở check-in/check-out cho người dùng';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Lấy danh sách người dùng cần nhắc nhở
        $users = User::all(); // Hoặc thêm điều kiện lọc nếu cần

        Log::info("Start sending reminders to users...");

        foreach ($users as $user) {
            Log::info("Sending reminder to {$user->email}");  // Log để kiểm tra xem email có đang được gửi không
            Mail::to($user->email)->send(new ReminderCheckinCheckout($user));
        }

        $this->info('Đã gửi email nhắc nhở cho tất cả người dùng.');
        Log::info("All reminder emails sent.");
    }
}
