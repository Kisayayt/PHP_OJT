<?php

namespace App\Http\Controllers;

use App\Mail\CheckInOutNotification;
use App\Models\User;
use App\Models\Configuration;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckInOutNotificationController extends Controller
{
    public function sendNotifications()
    {
        // Lấy thông tin cấu hình giờ check-in và check-out
        $config = Configuration::first();

        if (!$config || !$config->check_in_time || !$config->check_out_time) {
            return response()->json(['message' => 'Cấu hình giờ làm việc không hợp lệ.'], 400);
        }

        // Lấy tất cả người dùng đang hoạt động
        $users = User::where('is_active', 1)->get();

        // Lọc thời gian gửi email check-in
        $checkInTime = Carbon::parse($config->check_in_time);
        $checkOutTime = Carbon::parse($config->check_out_time);

        foreach ($users as $user) {
            // Gửi email check-in vào thời gian quy định
            if ($checkInTime->isSameMinute(Carbon::now())) {
                Mail::to($user->email)->send(new CheckInOutNotification('check_in'));
            }

            // Gửi email check-out vào thời gian quy định
            if ($checkOutTime->isSameMinute(Carbon::now())) {
                Mail::to($user->email)->send(new CheckInOutNotification('check_out'));
            }
        }

        return response()->json(['message' => 'Gửi thông báo thành công.'], 200);
    }
}
