<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayrollController extends Controller
{
    public function showPayrollForm()
    {
        $users = User::where('is_active', 1)
            ->where('role', '!=', 'admin')
            ->get();
        return view('payroll.index', compact('users'));
    }

    public function calculatePayroll(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // Lấy thông tin người dùng
        $user = User::findOrFail($request->user_id);

        Log::info("Lấy thông tin người dùng: {$user->name} (ID: {$user->id})");

        // Lấy thông tin mức lương của người dùng từ bảng salary_level_user
        $salaryLevel = DB::table('salary_level_user')
            ->join('salary_levels', 'salary_level_user.salary_level_id', '=', 'salary_levels.id')
            ->where('salary_level_user.user_id', $user->id)
            ->whereNull('salary_level_user.end_date') // Lấy mức lương hiện tại
            ->first();

        if (!$salaryLevel) {
            Log::warning("Không tìm thấy mức lương cho người dùng: {$user->name}");
            return;
        }

        $salaryCoefficient = $salaryLevel->salary_coefficient ?? 1;
        $monthlySalary = $salaryLevel->monthly_salary ?? 0;
        $dailySalary = $salaryLevel->daily_salary ?? 0;

        Log::info("Mức lương của người dùng: Hệ số lương: {$salaryCoefficient}, Lương tháng: {$monthlySalary}, Lương ngày: {$dailySalary}");

        // Tính số ngày làm việc trong tháng (không tính cuối tuần)
        $workDays = CarbonPeriod::create($monthStart, $monthEnd)
            ->filter(function ($date) {
                return !$date->isWeekend();
            });

        $totalWorkDays = $workDays->count();
        Log::info("Số ngày làm việc trong tháng: {$totalWorkDays}");

        // Lấy thông tin về thời gian đi làm của người dùng
        $attendances = DB::table('user_attendance')
            ->where('user_id', $user->id)
            ->where('type', 'out')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        Log::info("Lấy thông tin thời gian đi làm của người dùng: {$attendances->count()} bản ghi");

        // Tính số ngày công hợp lệ và không hợp lệ
        $validDays = $attendances->whereIn('status', [1, 5])->count();
        $invalidDays = $attendances->where('status', 0)->count();

        Log::info("Số ngày công hợp lệ: {$validDays}, Số ngày công không hợp lệ: {$invalidDays}");





        // Lấy thông tin các ngày nghỉ có lương trong tháng
        $paidLeaveRequests = DB::table('leave_requests')
            ->where('user_id', $user->id)
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->where('is_paid', true)
            ->where('status', 1) // Chỉ tính các ngày nghỉ có lương
            ->get();

        Log::info("Lấy thông tin các ngày nghỉ có lương: {$paidLeaveRequests->count()} bản ghi");

        // Tính tổng số ngày nghỉ có lương
        $paidLeaveDays = 0;
        foreach ($paidLeaveRequests as $leaveRequest) {
            // Nếu leave_type là 'morning' hoặc 'afternoon', thì tính 50% ngày nghỉ
            if ($leaveRequest->leave_type == 'morning' || $leaveRequest->leave_type == 'afternoon') {
                $paidLeaveDays += 0.5; // Thêm 0.5 ngày cho nghỉ buổi sáng hoặc chiều
            } else {
                // Nếu là nghỉ cả ngày, cộng số ngày đầy đủ (duration)
                $paidLeaveDays += $leaveRequest->duration;
            }

            Log::info("Ngày nghỉ có lương: ID: {$leaveRequest->id}, Loại nghỉ: {$leaveRequest->leave_type}, Số ngày: {$leaveRequest->duration}, Tổng số ngày nghỉ có lương hiện tại: {$paidLeaveDays}");
        }

        // Cộng thêm số ngày nghỉ có lương vào ngày công hợp lệ
        $validDays += $paidLeaveDays;
        // Tính số ngày công hợp lệ sau khi trừ đi phần trăm giảm trừ
        $deductionPercentage = $attendances->where('status', 5)->count() * 0.1; // Trừ 10% cho các đơn giải trình
        $effectiveValidDays = max(0, $validDays - $deductionPercentage); // Số ngày công hợp lệ sau giảm trừ
        Log::info("Ngày công hợp lệ hiệu quả (sau giảm trừ): {$effectiveValidDays}");
        Log::info("Ngày công hợp lệ sau khi cộng ngày nghỉ có lương: {$validDays}");

        // Tính lương nhận được
        if ($user->employee_role === 'official') {
            // Tính lương theo monthly_salary cho nhân viên chính thức
            $salaryReceived = (($monthlySalary * $salaryCoefficient) / $totalWorkDays) * $effectiveValidDays;
        } else {
            // Tính lương theo daily_salary cho nhân viên part-time
            $salaryReceived = $dailySalary * $validDays;
        }

        Log::info("Lương nhận được: {$salaryReceived}");

        // Truyền dữ liệu vào view
        return view('payroll.result', compact('user', 'validDays', 'invalidDays', 'salaryCoefficient', 'salaryReceived'));
    }






    public function storePayroll(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'salary_received' => 'required|numeric',
            'valid_days' => 'required|integer',
            'invalid_days' => 'required|integer',
            'salary_coefficient' => 'required|numeric',
        ]);


        $existingPayroll = Payroll::where('user_id', $request->user_id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->first();

        if ($existingPayroll) {

            $existingPayroll->salary_received = $request->salary_received;
            $existingPayroll->valid_days = $request->valid_days;
            $existingPayroll->invalid_days = $request->invalid_days;
            $existingPayroll->salary_coefficient = $request->salary_coefficient;
            $existingPayroll->save();
        } else {

            Payroll::create([
                'user_id' => $request->user_id,
                'salary_received' => $request->salary_received,
                'valid_days' => $request->valid_days,
                'invalid_days' => $request->invalid_days,
                'salary_coefficient' => $request->salary_coefficient,
            ]);
        }


        $user = User::find($request->user_id);
        $day = now()->format('d/m/Y');


        Mail::send('emails.salary_notification', [
            'day' => $day,
            'user' => $user->name,
            'salary_received' => $request->salary_received,
            'valid_days' => $request->valid_days,
            'invalid_days' => $request->invalid_days,
            'salary_coefficient' => $request->salary_coefficient,
        ], function ($email) use ($user) {
            $email->subject('Thông báo lương tháng ' . now()->format('m/Y'));
            $email->to($user->email, $user->name);
        });

        return redirect()->route('payroll.calculate')->with('success', 'Lương đã được lưu thành công và thông báo đã được gửi.');
    }

    public function showPayrolls(Request $request)
    {

        $search = $request->input('search');


        $payrolls = Payroll::with('user')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10);

        return view('payroll.payrolls', compact('payrolls', 'search'));
    }
}
