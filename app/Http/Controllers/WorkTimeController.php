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
        $workStart = strlen($request->work_start) === 5 ? $request->work_start . ':00' : $request->work_start;
        $workEnd = strlen($request->work_end) === 5 ? $request->work_end . ':00' : $request->work_end;

        $request->merge([
            'work_start' => $workStart,
            'work_end' => $workEnd,
        ]);

        $request->validate([
            'work_start' => 'required|date_format:H:i:s',
            'work_end' => 'required|date_format:H:i:s',
        ]);

        Configuration::where('name', 'work_start')->update(['time' => $workStart]);
        Configuration::where('name', 'work_end')->update(['time' => $workEnd]);

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
