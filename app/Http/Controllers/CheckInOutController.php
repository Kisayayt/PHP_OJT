<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_Attendance;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckInOutController extends Controller
{
    public function showCheckInOut()
    {
        $userId = auth()->id();
        $latestAttendance = User_Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        $latestCheckout = User_Attendance::where('user_id', $userId)
            ->where('type', 'out')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastCheckoutTime = $latestCheckout ? $latestCheckout->time : 0;
        $isCheckedIn = $latestAttendance && $latestAttendance->type == 'in';
        $time = $latestAttendance ? $latestAttendance->time : 0;

        $workStart = DB::table('configurations')->where('name', 'work_start')->value('time');
        $workEnd = DB::table('configurations')->where('name', 'work_end')->value('time');

        $history = User_Attendance::where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->paginate(7);

        return view('UserHome.index', compact('isCheckedIn', 'time', 'history', 'lastCheckoutTime', 'workStart', 'workEnd'));
    }

    public function checkIn(Request $request)
    {
        $userId = auth()->id();


        $workStart = DB::table('configurations')->where('name', 'work_start')->value('time');
        $workEnd = DB::table('configurations')->where('name', 'work_end')->value('time');


        $latestAttendance = User_Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();


        if ($latestAttendance && $latestAttendance->type == 'in') {
            return redirect('/home')->with(['errors' => 'Bạn đã check-in rồi!'], 400);
        }


        $currentTime = Carbon::now();
        $workStartCarbon = Carbon::parse($workStart);
        $workEndCarbon = Carbon::parse($workEnd);


        $status = 1;

        if ($currentTime->lt($workStartCarbon)) {
            $status = 1;
        } else {
            $status = 0;
        }


        $attendance = new User_Attendance();
        $attendance->user_id = $userId;
        $attendance->type = 'in';
        $attendance->time = 0;
        $attendance->status = $status;
        $attendance->created_at = $currentTime;
        $attendance->save();

        return redirect()->back();
    }



    public function checkOut(Request $request)
    {
        $userId = auth()->id();
        $latestAttendance = User_Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestAttendance || $latestAttendance->type == 'out') {
            return redirect('/home')->with(['errors' => 'Bạn chưa check-in!'], 400);
        }


        $checkInTime = $latestAttendance->created_at;
        $checkOutTime = now();
        $timeDifferenceInMinutes = $checkInTime->diffInMinutes($checkOutTime);


        $status = 1;
        $workStart = Carbon::parse(DB::table('configurations')->where('name', 'work_start')->value('time'));
        $workEnd = Carbon::parse(DB::table('configurations')->where('name', 'work_end')->value('time'));

        if ($checkInTime->lt($workStart) && $checkOutTime->gt($workEnd)) {
            $status = 1;
        } else {
            $status = 0;
        }


        $attendance = new User_Attendance();
        $attendance->user_id = $userId;
        $attendance->type = 'out';
        $attendance->time = $timeDifferenceInMinutes;
        $attendance->status = $status;
        $attendance->created_at = $checkOutTime;
        $attendance->save();

        return redirect()->back();
    }


    public function submitReason(Request $request, User_Attendance $attendance)
    {

        $reason = $request->reason;


        if ($reason === 'other') {
            $reason = $request->custom_reason;
        }

        $attendance->explanation = $reason;
        $attendance->status = 3;
        $attendance->save();

        $name = auth()->user()->name;
        Mail::send('emails.submitreason', compact('reason', 'name'), function ($email) {
            $user_email = auth()->user()->email;
            $email->subject('Đơn bạn đã nộp thành công!');
            $email->to($user_email, 'Vui thế thôi');
        });

        return redirect()->back()->with('success', 'Lý do đã được gửi!');
    }
}
