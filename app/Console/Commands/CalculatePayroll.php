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
        Log::info("Tháng này có: {$workDays->count()}");

        $totalWorkDays = $workDays->count();


        $users = User::with('salaryLevel')
            ->where('role', 'user')
            ->where('is_active', 1)
            ->get();

        if ($users->isEmpty()) {
            Log::warning("Không có nhân viên nào hợp lệ trong hệ thống để tính lương.");
            return;
        }

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
            $effectiveValidDays = max(0, $validDays - $deductionPercentage);

            $salaryReceived = (($monthlySalary * $salaryCoefficient) / $totalWorkDays) * $effectiveValidDays;


            Log::info("Tính lương cho nhân viên: {$user->name}, Lương nhận được: {$salaryReceived}, Ngày hợp lệ: {$validDays}, Ngày không hợp lệ: {$invalidDays}, Hệ số lương: {$salaryCoefficient}");


            DB::table('payrolls')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'created_at' => now()->startOfMonth(),
                ],
                [
                    'salary_received' => $salaryReceived,
                    'valid_days' => $validDays,
                    'invalid_days' => $invalidDays,
                    'salary_coefficient' => $salaryCoefficient,
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Lương của tất cả nhân viên hợp lệ đã được tính toán.');
    }
}
