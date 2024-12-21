<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculatePayroll extends Command
{
    protected $signature = 'payroll:calculate {--testTime=}';
    protected $description = 'Tính lương cho tất cả nhân viên vào cuối ngày';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $payTimeConfig = Configuration::where('name', 'payTime')->first();

        if (!$payTimeConfig || !$payTimeConfig->time) {
            Log::warning("Không tìm thấy cấu hình thời gian tính lương hoặc giá trị time trống.");
            return;
        }

        $payTime = Carbon::createFromFormat('H:i:s', $payTimeConfig->time);

        $testTime = $this->option('testTime');
        $currentTime = $testTime
            ? Carbon::createFromFormat('H:i:s', $testTime)
            : Carbon::now();

        Log::info("Thời gian tính lương từ cấu hình: {$payTime->format('H:i:s')}");
        Log::info("Thời gian hiện tại để kiểm tra: {$currentTime->format('H:i:s')}");

        if (!$currentTime->greaterThanOrEqualTo($payTime)) {
            Log::info("Thời gian hiện tại ({$currentTime->format('H:i:s')}) chưa đạt đến thời gian tính lương ({$payTime->format('H:i:s')}).");
            return;
        }

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $workDays = CarbonPeriod::create($monthStart, $monthEnd)
            ->filter(function ($date) {
                return !$date->isWeekend();
            });
        Log::info("Tháng này có: {$workDays->count()} ngày làm việc.");

        $totalWorkDays = $workDays->count();

        $users = User::where('role', 'user')
            ->where('is_active', 1)
            ->get();

        if ($users->isEmpty()) {
            Log::warning("Không có nhân viên nào hợp lệ trong hệ thống để tính lương.");
            return;
        }

        foreach ($users as $user) {
            // Lấy thông tin mức lương từ bảng salary_level_user
            $salaryLevel = DB::table('salary_level_user')
                ->join('salary_levels', 'salary_level_user.salary_level_id', '=', 'salary_levels.id')
                ->where('salary_level_user.user_id', $user->id)
                ->whereNull('salary_level_user.end_date')
                ->first();

            if (!$salaryLevel) {
                Log::warning("Không tìm thấy mức lương cho nhân viên: {$user->name}");
                continue;
            }

            $salaryCoefficient = $salaryLevel->salary_coefficient ?? 1;
            $monthlySalary = $salaryLevel->monthly_salary ?? 0;
            $dailySalary = $salaryLevel->daily_salary ?? 0;

            // Lấy thông tin thời gian đi làm
            $attendances = DB::table('user_attendance')
                ->where('user_id', $user->id)
                ->where('type', 'out')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->get();

            $validDays = $attendances->whereIn('status', [1, 5])->count();
            $invalidDays = $attendances->where('status', 0)->count();



            // Lấy thông tin các ngày nghỉ có lương trong tháng
            $paidLeaveRequests = DB::table('leave_requests')
                ->where('user_id', $user->id)
                ->whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                ->where('is_paid', true)
                ->where('status', 1) // Chỉ tính các ngày nghỉ có lương
                ->get();

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
            }

            // Cộng thêm số ngày nghỉ có lương vào ngày hợp lệ
            $validDays += $paidLeaveDays;
            $deductionPercentage = $attendances->where('status', 5)->count() * 0.1;
            $effectiveValidDays = max(0, $validDays - $deductionPercentage);
            // Tính lương nhận được
            if ($user->employee_role === 'official') {
                // Tính lương theo monthly_salary
                $salaryReceived = (($monthlySalary * $salaryCoefficient) / $totalWorkDays) * $effectiveValidDays;
            } else if ($user->employee_role === 'part_time') {
                // Tính lương theo daily_salary
                $salaryReceived = $dailySalary * $validDays;
            } else {
                Log::warning("Không xác định được role cho nhân viên: {$user->name}");
                continue;
            }

            Log::info("Tính lương cho nhân viên: {$user->name}, Role: {$user->employee_role}, Lương nhận được: {$salaryReceived}, Ngày hợp lệ: {$validDays}, Ngày không hợp lệ: {$invalidDays}, Hệ số lương: {$salaryCoefficient}");

            // Kiểm tra nếu bản ghi tính lương của tháng này đã tồn tại
            $existingPayroll = DB::table('payrolls')
                ->where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->first();

            if ($existingPayroll) {
                // Nếu bản ghi đã tồn tại, tiến hành cập nhật
                DB::table('payrolls')
                    ->where('id', $existingPayroll->id)
                    ->update([
                        'salary_received' => $salaryReceived,
                        'valid_days' => $validDays,
                        'invalid_days' => $invalidDays,
                        'salary_coefficient' => $salaryCoefficient,
                        'updated_at' => now(), // Cập nhật thời gian cập nhật
                    ]);
            } else {
                // Nếu bản ghi chưa tồn tại, tạo mới
                DB::table('payrolls')->insert([
                    'user_id' => $user->id,
                    'salary_received' => $salaryReceived,
                    'valid_days' => $validDays,
                    'invalid_days' => $invalidDays,
                    'salary_coefficient' => $salaryCoefficient,
                    'created_at' => now(), // Lưu thời gian tạo bản ghi
                    'updated_at' => now(), // Lưu thời gian cập nhật
                ]);
            }
        }

        $this->info('Lương của tất cả nhân viên hợp lệ đã được tính toán.');
    }
}
