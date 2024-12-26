<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User_Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        // Xác thực request
        $request->validate([
            'leave_type' => 'required|in:morning,afternoon,full_day,multiple_days',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date', // Quy tắc này chỉ áp dụng nếu cần
            'reason' => 'nullable|string|max:255',
        ]);

        if ($request->leave_type === 'multiple_days' && !$request->end_date) {
            return redirect()->back()->withErrors(['error' => 'Vui lòng chọn ngày kết thúc khi đăng ký nghỉ nhiều ngày.']);
        }

        if (in_array($request->leave_type, ['morning', 'afternoon', 'full_day']) && $request->end_date) {
            return redirect()->back()->withErrors(['error' => 'Bạn không cần chọn ngày kết thúc cho loại nghỉ này.']);
        }

        // Kiểm tra nếu đã có đơn nghỉ trong ngày
        // $existingLeave = DB::table('leave_requests')
        //     ->where('user_id', $user->id)
        //     ->where(function ($query) use ($request) {
        //         $query->whereBetween('start_date', [$request->start_date, $request->end_date])
        //             ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
        //     })
        //     ->exists();

        // if ($existingLeave) {
        //     return redirect()->back()->withErrors(['error' => 'Bạn đã xin nghỉ phép trong ngày nay.']);
        // }

        // Kiểm tra ngày nghỉ không được đăng ký trước ngày hiện tại (có thể điều chỉnh theo yêu cầu)
        // Kiểm tra ngày nghỉ không được đăng ký trước ngày hiện tại (trừ trường hợp nghỉ buổi sáng hoặc buổi chiều)
        if (!in_array($request->leave_type, ['morning', 'afternoon']) && strtotime($request->start_date) < strtotime('today')) {
            return redirect()->back()->withErrors(['error' => 'Bạn không thể đăng ký nghỉ cho ngày trước hiện tại.']);
        }


        // Tính toán số ngày nghỉ
        $duration = $request->leave_type === 'multiple_days'
            ? (strtotime($request->end_date) - strtotime($request->start_date)) / 86400 + 1
            : 1;

        // Kiểm tra ngày nghỉ có lương hay không
        $isPaid = $user->leave_balance >= $duration;

        // Tạo đơn nghỉ phép
        DB::table('leave_requests')->insert([
            'user_id' => $user->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?? $request->start_date,
            'leave_type' => $request->leave_type,
            'reason' => $request->reason,
            'duration' => $duration,
            'status' => 0, // Pending
            'is_paid' => $isPaid,
            'created_at' => now(),
            'updated_at' => now(),
            'approved_by' => null,
        ]);

        // Trừ số ngày nghỉ nếu đơn nghỉ có lương
        if ($isPaid) {
            DB::table('users')
                ->where('id', $user->id)
                ->decrement('leave_balance', $duration);
        }

        return redirect()->back()->with('success', 'Đơn nghỉ phép đã được gửi thành công!');
    }

    public function index()
    {
        $user = auth()->user();
        $userId = auth()->id();
        $leaveRequests = DB::table('leave_requests')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $latestCheckout = User_Attendance::where('user_id', $userId)
            ->where('type', 'out')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastCheckoutTime = $latestCheckout ? $latestCheckout->time : 0;

        // dd($leaveRequests); // Kiểm tra cấu trúc dữ liệu
        return view('leave_requests.index', compact('leaveRequests', 'lastCheckoutTime'));
    }


    public function destroy($id)
    {
        $leaveRequest = DB::table('leave_requests')->find($id);

        if (!$leaveRequest || $leaveRequest->user_id !== auth()->id()) {
            return redirect()->route('leave_requests_user.index')->withErrors('Không tìm thấy đơn nghỉ phép!');
        }

        DB::table('leave_requests')->delete($id);

        return redirect()->route('leave_requests_user.index')->with('success', 'Đơn nghỉ phép đã được xóa!');
    }

    public function edit($id)
    {
        $user = auth()->user();

        // Lấy thông tin đơn nghỉ phép
        $leaveRequest = DB::table('leave_requests')
            ->where('id', $id)
            ->where('user_id', $user->id) // Đảm bảo chỉ chỉnh sửa đơn của chính mình
            ->first();
        // dd($leaveRequest);
        if (!$leaveRequest) {
            return redirect()->route('leave_requests_user.index')->withErrors(['error' => 'Không tìm thấy đơn nghỉ phép.']);
        }

        return view('leave_requests.edit', compact('leaveRequest'));
    }

    // Cập nhật đơn nghỉ phép
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        // Xác thực dữ liệu từ form
        $request->validate([
            'leave_type' => 'required|in:morning,afternoon,full_day,multiple_days',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);

        // Kiểm tra đơn có tồn tại và thuộc về người dùng
        $leaveRequest = DB::table('leave_requests')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$leaveRequest) {
            return redirect()->route('leave_requests_user.index')->withErrors(['error' => 'Không tìm thấy đơn nghỉ phép.']);
        }

        // Kiểm tra điều kiện logic
        if ($request->leave_type === 'multiple_days' && !$request->end_date) {
            return redirect()->back()->withErrors(['error' => 'Vui lòng chọn ngày kết thúc khi đăng ký nghỉ nhiều ngày.']);
        }

        if (in_array($request->leave_type, ['morning', 'afternoon', 'full_day']) && $request->end_date) {
            return redirect()->back()->withErrors(['error' => 'Bạn không cần chọn ngày kết thúc cho loại nghỉ này.']);
        }

        // Cập nhật dữ liệu vào database
        DB::table('leave_requests')
            ->where('id', $id)
            ->update([
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date ?? $request->start_date,
                'reason' => $request->reason,
                'updated_at' => now(),
            ]);

        return redirect()->route('leave_requests_user.index')->with('success', 'Đơn nghỉ phép đã được cập nhật thành công!');
    }
}
