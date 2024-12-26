<?php

namespace App\Http\Controllers;

use App\Mail\ReminderCheckinCheckout;
use App\Models\Configuration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WorkTimeController extends Controller
{
    public function showWorkTime()
    {
        $workStart = Configuration::where('name', 'work_start')->value('time');
        $workEnd = Configuration::where('name', 'work_end')->value('time');
        $reminder = Configuration::where('name', 'reminder')->value('time');

        return view('workTime.index', compact('workStart', 'workEnd', 'reminder'));
    }

    public function updateWorkTime(Request $request)
    {
        // Chuyển đổi thời gian bắt đầu và kết thúc thành định dạng chuẩn (H:i:s)
        $workStart = strlen($request->work_start) === 5 ? $request->work_start . ':00' : $request->work_start;
        $workEnd = strlen($request->work_end) === 5 ? $request->work_end . ':00' : $request->work_end;

        // Gắn giá trị đã chuyển đổi vào request
        $request->merge([
            'work_start' => $workStart,
            'work_end' => $workEnd,
        ]);

        // Validate các trường thời gian
        $request->validate([
            'work_start' => 'required|date_format:H:i:s',
            'work_end' => 'required|date_format:H:i:s',
        ]);

        // Kiểm tra nếu work_end phải lớn hơn work_start
        if (strtotime($workStart) >= strtotime($workEnd)) {
            return redirect()->back()->withErrors(['work_end' => 'Thời gian kết thúc phải sau thời gian bắt đầu.']);
        }

        // Cập nhật thời gian làm việc trong bảng Configuration
        Configuration::where('name', 'work_start')->update(['time' => $workStart]);
        Configuration::where('name', 'work_end')->update(['time' => $workEnd]);

        // Trả về thông báo thành công
        return redirect()->route('admin.workTime')->with('success', 'Thời gian làm việc đã được cập nhật!');
    }



    public function updateReminder(Request $request)
    {

        $reminderTime = strlen($request->reminder) === 5 ? $request->reminder . ':00' : $request->reminder;


        $request->merge([
            'reminder' => $reminderTime,
        ]);


        $request->validate([
            'reminder' => 'required|date_format:H:i:s',
        ]);


        Configuration::where('name', 'reminder')->update(['time' => $reminderTime]);


        return redirect()->route('admin.workTime')->with('success', 'Thời gian reminder đã được cập nhật!');
    }


    public function sendReminders(Request $request)
    {

        $users = User::all();

        Log::info("Start sending reminders to users...");

        foreach ($users as $user) {
            Log::info("Sending reminder to {$user->email}");
            Mail::to($user->email)->send(new ReminderCheckinCheckout($user));
        }

        return redirect()->back()->with('status', 'Đã gửi email nhắc nhở cho tất cả người dùng.');
    }
}
