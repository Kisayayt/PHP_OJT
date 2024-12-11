<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class AdminLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());
        $query = LeaveRequest::with(['user', 'approvedBy']);

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', intval($request->status));
        }

        // Lọc theo ngày bắt đầu
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $query->where('start_date', '>=', $startDate);
        }

        // Lọc theo ngày kết thúc
        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = date('Y-m-d', strtotime($request->end_date));
            $query->where('start_date', '<=', $endDate);
        }

        // Lọc theo nhân viên
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(7);

        return view('adminLeaveRequests.index', compact('leaveRequests'));
    }





    public function updateStatus(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'status' => 'required|in:1,2', // Chỉ chấp nhận giá trị số 1 (chấp nhận) và 2 (từ chối)
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        // Cập nhật trạng thái
        $leaveRequest->status = intval($request->status); // Đảm bảo `status` là số
        $leaveRequest->approved_by = auth()->id(); // Lưu ID của người phê duyệt
        $leaveRequest->save();

        return redirect()->route('leave_requests.index')
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}
