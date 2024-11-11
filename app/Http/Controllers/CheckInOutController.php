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

        // Lấy giờ làm việc từ bảng Configuration
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

        // Lấy giờ làm việc từ bảng Configuration
        $workStart = DB::table('configurations')->where('name', 'work_start')->value('time');
        $workEnd = DB::table('configurations')->where('name', 'work_end')->value('time');

        // Kiểm tra lần check-in trước đó
        $latestAttendance = User_Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Kiểm tra nếu người dùng đã check-in trước đó
        if ($latestAttendance && $latestAttendance->type == 'in') {
            return redirect('/home')->with(['errors' => 'Bạn đã check-in rồi!'], 400);
        }

        // Kiểm tra giờ hiện tại
        $currentTime = Carbon::now();
        $workStartCarbon = Carbon::parse($workStart);
        $workEndCarbon = Carbon::parse($workEnd);

        // Kiểm tra thời gian check-in hợp lệ
        $status = 1; // Mặc định là hợp lệ

        if ($currentTime->lt($workStartCarbon) || $currentTime->gt($workEndCarbon)) {
            $status = 0; // Không hợp lệ
        }

        // Tạo bản ghi check-in
        $attendance = new User_Attendance();
        $attendance->user_id = $userId;
        $attendance->type = 'in';
        $attendance->time = 0; // Chưa tính thời gian
        $attendance->status = $status; // Cập nhật trạng thái hợp lệ hay không
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

        // Tính thời gian check-out
        $checkInTime = $latestAttendance->created_at;
        $checkOutTime = now();
        $timeDifference = $checkOutTime->diffInHours($checkInTime);

        // Cập nhật trạng thái check-out
        $status = 1; // Mặc định là hợp lệ

        // Kiểm tra nếu check-in < 08:00 và check-out > 17:00 thì trạng thái hợp lệ
        $workStart = Carbon::parse(DB::table('configurations')->where('name', 'work_start')->value('time'));
        $workEnd = Carbon::parse(DB::table('configurations')->where('name', 'work_end')->value('time'));

        if ($checkInTime->lt($workStart) && $checkOutTime->gt($workEnd)) {
            $status = 1; // Hợp lệ
        } else {
            $status = 0; // Không hợp lệ
        }

        // Tạo bản ghi check-out
        $attendance = new User_Attendance();
        $attendance->user_id = $userId;
        $attendance->type = 'out';
        $attendance->time = $timeDifference;
        $attendance->status = $status; // Cập nhật trạng thái hợp lệ hay không
        $attendance->created_at = $checkOutTime;
        $attendance->save();

        return redirect()->back();
    }

    public function submitReason(Request $request, User_Attendance $attendance)
    {
        $attendance->explanation = $request->reason;
        $attendance->status = 3; // Đang xem xét
        $attendance->save();

        return redirect()->back()->with('success', 'Lý do đã được gửi!');
    }
}
