<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Lấy thông tin mức lương của người dùng từ bảng salary_level_user
        $salaryLevel = DB::table('salary_level_user')
            ->join('salary_levels', 'salary_level_user.salary_level_id', '=', 'salary_levels.id')
            ->where('salary_level_user.user_id', $user->id)
            ->whereNull('salary_level_user.end_date') // Nếu bạn muốn lấy mức lương hiện tại, bỏ filter này nếu bạn muốn xem lịch sử
            ->first();

        $salaryCoefficient = $salaryLevel->salary_coefficient ?? 1;
        $monthlySalary = $salaryLevel->monthly_salary ?? 0;

        // Tính số ngày công trong tháng
        $workDays = CarbonPeriod::create($monthStart, $monthEnd)
            ->filter(function ($date) {
                return !$date->isWeekend();
            });

        $totalWorkDays = $workDays->count();

        // Lấy thông tin về thời gian đi làm của người dùng
        $attendances = DB::table('user_attendance')
            ->where('user_id', $user->id)
            ->where('type', 'out')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        // Tính số ngày công hợp lệ và không hợp lệ
        $validDays = $attendances->whereIn('status', [1, 5])->count();
        $invalidDays = $attendances->where('status', 0)->count();

        // Tính số ngày công hợp lệ sau khi trừ đi phần trăm giảm trừ
        $deductionPercentage = $attendances->where('status', 5)->count() * 0.1;
        $effectiveValidDays = $validDays - $deductionPercentage;

        // Tính lương nhận được
        $salaryReceived = (($monthlySalary * $salaryCoefficient) / $totalWorkDays) * $effectiveValidDays;

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
