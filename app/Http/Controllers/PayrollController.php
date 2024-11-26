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

        $user = User::with('salaryLevel')->findOrFail($request->user_id);

        $salaryCoefficient = $user->salaryLevel->salary_coefficient ?? 1;
        $monthlySalary = $user->salaryLevel->monthly_salary ?? 0;

        $workDays = CarbonPeriod::create($monthStart, $monthEnd)
            ->filter(function ($date) {

                return !$date->isWeekend();
            });

        $totalWorkDays = $workDays->count();

        $attendances = DB::table('user_attendance')
            ->where('user_id', $user->id)
            ->where('type', 'out')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();


        $validDays = $attendances->whereIn('status', [1, 5])->count();
        $invalidDays = $attendances->where('status', 0)->count();


        $deductionPercentage = $attendances->where('status', 5)->count() * 0.1;
        $effectiveValidDays = $validDays - $deductionPercentage;


        $salaryReceived = (($monthlySalary * $salaryCoefficient) / $totalWorkDays) * $effectiveValidDays;


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

        // Kiểm tra xem nhân viên đã có bảng lương trong tháng này chưa
        $existingPayroll = Payroll::where('user_id', $request->user_id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->first();

        if ($existingPayroll) {
            // Cập nhật bảng lương nếu đã có
            $existingPayroll->salary_received = $request->salary_received;
            $existingPayroll->valid_days = $request->valid_days;
            $existingPayroll->invalid_days = $request->invalid_days;
            $existingPayroll->salary_coefficient = $request->salary_coefficient;
            $existingPayroll->save();
        } else {
            // Tạo mới bảng lương nếu chưa có
            Payroll::create([
                'user_id' => $request->user_id,
                'salary_received' => $request->salary_received,
                'valid_days' => $request->valid_days,
                'invalid_days' => $request->invalid_days,
                'salary_coefficient' => $request->salary_coefficient,
            ]);
        }

        // Lấy thông tin nhân viên để gửi email
        $user = User::find($request->user_id);
        $day = now()->format('d/m/Y');
        // dd($user);
        // Gửi email thông báo cho nhân viên
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
        // Lấy từ input tìm kiếm
        $search = $request->input('search');

        // Lọc payrolls theo tên nhân viên
        $payrolls = Payroll::with('user')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%'); // Tìm kiếm theo tên nhân viên
                });
            })
            ->paginate(10);

        return view('payroll.payrolls', compact('payrolls', 'search'));
    }
}
