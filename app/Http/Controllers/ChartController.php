<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{

    public function chartView()
    {
        $departments = DB::table('departments')->where('is_active', 1)->get();
        return view('charts.index', ['departments' => $departments]);
    }

    public function view()
    {
        $departments = DB::table('departments')->where('is_active', 1)->get();
        return view('charts.attendance_chart', ['departments' => $departments]);
    }

    public function getEmployeeRatioByDepartment()
    {
        $data = DB::table('users')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->select('departments.name as department_name', DB::raw('count(users.id) as employee_count'))
            ->groupBy('departments.name')
            ->get();

        return response()->json([
            'labels' => $data->pluck('department_name'),
            'counts' => $data->pluck('employee_count'),
        ]);
    }

    public function genderStatistics(Request $request)
    {
        // Lấy department_id từ query string (nếu có)
        $departmentId = $request->query('department_id');

        // Lấy danh sách departments có liên kết với users
        $query = Departments::with(['users']);

        // Nếu có departmentId, lọc theo phòng ban
        if ($departmentId) {
            $query->where('id', $departmentId);
        }

        $departments = $query->get();

        // Xử lý dữ liệu để đếm giới tính
        $data = $departments->map(function ($dept) {
            $genderCounts = $dept->users->groupBy('gender')->map(function ($group) {
                return $group->count();
            });

            return [
                'department' => $dept->name,
                'male' => $genderCounts['male'] ?? 0,
                'female' => $genderCounts['female'] ?? 0,
                'other' => $genderCounts['other'] ?? 0,
            ];
        });

        return response()->json($data);
    }

    public function getTotalEmployees()
    {
        $totalEmployees = DB::table('users')
            ->where('is_active', 1)
            ->where('role', 'user')
            ->count();

        return response()->json(['totalEmployees' => $totalEmployees]);
    }

    public function getAttendanceStats(Request $request)
    {
        $filterDays = $request->query('filter');
        $query = DB::table('user_attendance')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as day"), // Nhóm theo ngày
                DB::raw("SUM(CASE WHEN status IN (1, 5) THEN 1 ELSE 0 END) as valid_days"), // Ngày hợp lệ
                DB::raw("SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as invalid_days") // Ngày không hợp lệ
            )
            ->where('type', 'out');

        // Áp dụng bộ lọc ngày nếu có
        if ($filterDays) {
            $startDate = now()->subDays($filterDays)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        $stats = $query->groupBy('day') // Nhóm theo ngày
            ->orderBy('day', 'ASC')
            ->get();

        return response()->json($stats);
    }




    public function getAgeGenderStatsByDepartment()
    {
        $data = DB::table('users')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->select(
                'departments.name as department_name',
                DB::raw('
                CASE 
                    WHEN users.age BETWEEN 18 AND 30 THEN "18-30"
                    WHEN users.age BETWEEN 31 AND 45 THEN "31-45"
                    ELSE "46+" 
                END as age_group'),
                'users.gender',
                DB::raw('count(users.id) as total')
            )
            ->groupBy('department_name', 'age_group', 'users.gender')
            ->get();

        return response()->json($data);
    }

    public function getContractTypeByDepartment()
    {
        $data = DB::table('users')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->select(
                'departments.name as department_name',
                'users.employee_role',
                DB::raw('COUNT(users.id) as total')
            )
            ->groupBy('departments.name', 'users.employee_role')
            ->get();

        return response()->json($data);
    }
}
