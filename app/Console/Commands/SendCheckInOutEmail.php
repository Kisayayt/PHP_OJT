<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Mail\CheckInOutNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCheckInOutEmail extends Command
{
    protected $signature = 'send:checkin-out-email';
    protected $description = 'Gửi email nhắc nhở giờ check-in/check-out';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentTime = now()->format('H:i:s');  // Lấy thời gian hiện tại
        $checkInConfig = Configuration::where('name', 'check_in')->first();
        $checkOutConfig = Configuration::where('name', 'check_out')->first();

        // Gửi email nhắc nhở check-in
        if ($currentTime === $checkInConfig->time) {
            $users = User::where('is_active', 1)->get();
            foreach ($users as $user) {
                Mail::to($user->email)->send(new CheckInOutNotification('check_in'));
            }
        }

        // Gửi email nhắc nhở check-out
        if ($currentTime === $checkOutConfig->time) {
            $users = User::where('is_active', 1)->get();
            foreach ($users as $user) {
                Mail::to($user->email)->send(new CheckInOutNotification('check_out'));
            }
        }
    }
}
