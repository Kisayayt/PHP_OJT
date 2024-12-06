<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{

    public function chartView()
    {
        return view('charts.index');
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
