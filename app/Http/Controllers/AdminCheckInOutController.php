<?php

namespace App\Http\Controllers;

use App\Models\User_Attendance;
use Illuminate\Http\Request;

class AdminCheckInOutController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');
        $date = $request->input('date');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');


        $query = User_Attendance::with('user');


        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })->orWhere('created_at', 'LIKE', "%{$search}%");
        }


        if ($date) {
            $query->whereDate('created_at', $date);
        }


        $attendanceRecords = $query->orderBy($sortBy, $sortDirection)->paginate(6);

        return view('checkin.index', compact('attendanceRecords'));
    }




    public function search(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $attendanceRecords = User_Attendance::with('user')
            ->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhere('created_at', 'LIKE', "%{$search}%")
            ->orWhere('updated_at', 'LIKE', "%{$search}%")
            ->orderBy($sortBy, $sortDirection)
            ->paginate(6);

        return view('checkin.index', compact('attendanceRecords', 'sortBy', 'sortDirection'));
    }


    public function filterByDate(Request $request)
    {
        $date = $request->input('date');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($date) {
            $attendanceRecords = User_Attendance::with('user')
                ->whereDate('created_at', $date)
                ->orderBy($sortBy, $sortDirection)
                ->paginate(6);
        } else {
            $attendanceRecords = User_Attendance::with('user')
                ->orderBy($sortBy, $sortDirection)
                ->paginate(6);
        }

        return view('checkin.index', compact('attendanceRecords', 'sortBy', 'sortDirection'));
    }

    public function pendingRequests()
    {
        // Lấy các bản ghi `check-out` có `status = 3`
        $pendingRequests = User_Attendance::where('status', 3)
            ->where('type', 'out')
            ->with(['checkInRecord'])
            ->paginate(5);

        return view('checkin.requests', compact('pendingRequests'));
    }



    public function acceptRequest($id)
    {
        $request = User_Attendance::findOrFail($id);
        $request->status = 1; // Đặt status về hợp lệ
        $request->save();

        return redirect()->route('admin.requests')->with('success', 'Đơn đã được chấp nhận.');
    }

    public function rejectRequest($id)
    {
        $request = User_Attendance::findOrFail($id);
        $request->status = 4; // Đặt status về không hợp lệ
        $request->save();

        return redirect()->route('admin.requests')->with('error', 'Đơn đã bị từ chối.');
    }
}
