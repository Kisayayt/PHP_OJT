<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculatePayroll extends Command
{
    protected $signature = 'payroll:calculate';
    protected $description = 'Tính lương cho tất cả nhân viên vào cuối ngày';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Lấy thời gian từ cấu hình trong DB (name: payTime)
        $payTimeConfig = Configuration::where('name', 'payTime')->first();

        if (!$payTimeConfig) {
            Log::warning("Không tìm thấy cấu hình thời gian tính lương.");
            return;
        }

        $payTime = $payTimeConfig->time; // Thời gian tính lương trong DB (23:00:00)
        $currentTime = Carbon::now()->format('H:i:s'); // Sử dụng Carbon để lấy thời gian hiện tại

        Log::info("Reminder time from configuration: {$payTime}");
        Log::info("Current time from Carbon: {$currentTime}");

        // Kiểm tra nếu thời gian hiện tại đã đến giờ tính lương
        if ($currentTime !== $payTime) {
            Log::info("Thời gian hiện tại ({$currentTime}) chưa đạt đến thời gian tính lương ({$payTime}).");
            return;
        }

        // Tiến hành tính lương cho tất cả nhân viên
        $users = User::with('salaryLevel')->get();

        foreach ($users as $user) {
            $salaryCoefficient = $user->salaryLevel->salary_coefficient ?? 1;
            $monthlySalary = $user->salaryLevel->monthly_salary ?? 0;

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

            $salaryReceived = (($monthlySalary * $salaryCoefficient) / 23) * $effectiveValidDays;

            // Log kết quả tính lương
            Log::info("Tính lương cho nhân viên: {$user->name}, Lương nhận được: {$salaryReceived}");

            // Lưu thông tin lương vào bảng payroll
            DB::table('payroll')->insert([
                'user_id' => $user->id,
                'salary_received' => $salaryReceived,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('Lương của tất cả nhân viên đã được tính toán.');
    }
}
