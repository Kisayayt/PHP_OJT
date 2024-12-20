<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\Request;

class AnotherChartController extends Controller
{
    public function index()
    {
        $genderStats = $this->getGenderStats();
        $contractStats = $this->getContractStats();
        $attendanceStats = $this->getAttendanceStats();

        return view('charts.gender', compact('genderStats', 'contractStats', 'attendanceStats'));
    }

    private function getGenderStats()
    {
        // Query dữ liệu giới tính theo phòng ban
        return Departments::with(['users' => function ($query) {
            $query->selectRaw('department_id, gender, COUNT(*) as count')
                ->groupBy('department_id', 'gender');
        }])->get();
    }

    private function getContractStats()
    {
        // Query loại hợp đồng theo phòng ban
        // Thay bằng query thực tế từ cơ sở dữ liệu của bạn
        return [];
    }

    private function getAttendanceStats()
    {
        // Query ngày công hợp lệ/không hợp lệ
        // Thay bằng query thực tế từ cơ sở dữ liệu của bạn
        return [];
    }
}
