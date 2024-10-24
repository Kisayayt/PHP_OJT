<?php

namespace App\Http\Controllers;

use App\Models\User_Attendance;
use Illuminate\Http\Request;

class AdminCheckInOutController extends Controller
{
    public function index()
    {

        $attendanceRecords = User_Attendance::with('user')->orderBy('created_at', 'desc')->paginate(5);

        return view('checkin.index', compact('attendanceRecords'));
    }



    public function search(Request $request)
    {
        $search = $request->input('search');

        $attendanceRecords = User_Attendance::with('user')
            ->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhere('created_at', 'LIKE', "%{$search}%")
            ->orWhere('updated_at', 'LIKE', "%{$search}%")
            ->paginate(5);

        return view('checkin.index', compact('attendanceRecords'));
    }

    public function filterByDate(Request $request)
    {
        $date = $request->input('date');

        if ($date) {

            $attendanceRecords = User_Attendance::with('user')
                ->whereDate('created_at', $date)
                ->paginate(5);
        } else {

            $attendanceRecords = User_Attendance::with('user')->paginate(5);
        }

        return view('checkin.index', compact('attendanceRecords'));
    }
}
