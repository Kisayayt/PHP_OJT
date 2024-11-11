<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAttendanceReminderEmails extends Command
{
    protected $signature = 'email:send-attendance-reminder';
    protected $description = 'Gửi email nhắc nhở nhân viên check-in/check-out';

    public function handle()
    {
        $currentTime = Carbon::now()->format('H:i');
        $checkinTime = env('CHECKIN_TIME', '08:00');
        $checkoutTime = env('CHECKOUT_TIME', '17:00');

        $users = User::where('is_active', true)->get();

        foreach ($users as $user) {
            if ($currentTime === $checkinTime || $currentTime === $checkoutTime) {
                $type = $currentTime === $checkinTime ? 'check-in' : 'check-out';

                Mail::raw("Xin chào {$user->name}, đây là nhắc nhở để bạn thực hiện {$type}.", function ($message) use ($user) {
                    $message->to($user->email)->subject('Nhắc nhở Check-in/Check-out');
                });
            }
        }

        $this->info('Email nhắc nhở đã được gửi.');
    }
}
