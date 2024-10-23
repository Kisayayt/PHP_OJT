<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_Attendance;
use Carbon\Carbon;
use Auth;

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
        $history = User_Attendance::where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(7);

        return view('UserHome.index', compact('isCheckedIn', 'time', 'history', 'lastCheckoutTime'));
    }

    public function checkIn(Request $request)
    {
        $userId = auth()->id();


        $latestAttendance = User_Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestAttendance && $latestAttendance->type == 'in') {
            return response()->json(['message' => 'Bạn đã check-in rồi!'], 400);
        }


        $attendance = new User_Attendance();
        $attendance->user_id = $userId;
        $attendance->type = 'in';
        $attendance->time = 0;
        $attendance->created_at = now();
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
            return response()->json(['message' => 'Bạn chưa check-in!'], 400);
        }


        $checkInTime = $latestAttendance->created_at;
        $checkOutTime = now();
        $timeDifference = $checkOutTime->diffInHours($checkInTime);


        $attendance = new User_Attendance();
        $attendance->user_id = $userId;
        $attendance->type = 'out';
        $attendance->time = $timeDifference;
        $attendance->created_at = $checkOutTime;
        $attendance->save();

        return redirect()->back();
    }
}
