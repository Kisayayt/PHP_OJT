<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_Attendance;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\DB;

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

        // Tính toán thời gian chênh lệch giữa check-in và check-out
        $checkInTime = $latestAttendance->created_at;
        $checkOutTime = now();
        $timeDifferenceInMinutes = $checkInTime->diffInMinutes($checkOutTime);  // Tính bằng phút

        // Kiểm tra trạng thái (hợp lệ hay không hợp lệ)
        $status = 1;  // Mặc định là hợp lệ
        $workStart = Carbon::parse(DB::table('configurations')->where('name', 'work_start')->value('time'));
        $workEnd = Carbon::parse(DB::table('configurations')->where('name', 'work_end')->value('time'));

        if ($checkInTime->lt($workStart) && $checkOutTime->gt($workEnd)) {
            $status = 1;  // Hợp lệ nếu check-in trước giờ làm việc và check-out sau giờ làm việc
        } else {
            $status = 0;  // Không hợp lệ
        }

        // Lưu vào cơ sở dữ liệu (lưu số phút)
        $attendance = new User_Attendance();
        $attendance->user_id = $userId;
        $attendance->type = 'out';
        $attendance->time = $timeDifferenceInMinutes;  // Lưu số phút
        $attendance->status = $status;
        $attendance->created_at = $checkOutTime;
        $attendance->save();

        return redirect()->back();
    }


    public function submitReason(Request $request, User_Attendance $attendance)
    {
        $attendance->explanation = $request->reason;
        $attendance->status = 3;
        $attendance->save();

        return redirect()->back()->with('success', 'Lý do đã được gửi!');
    }
}
