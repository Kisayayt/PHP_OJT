<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function departmentDashboard()
    {
        $departments = Departments::with('parent')->paginate(5);

        return view('departments.index')->with('departments', $departments);
    }

    public function insertDepartmentView()
    {
        $departments = Departments::all();
        return view('departments.create', [
            'departments' => $departments
        ]);
    }

    public function insertDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        Departments::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'status' => '1',
        ]);

        return redirect('/departmentDashboard');
    }

    public function deleteDepartment($id)
    {
        $department = Departments::find($id);
        $department->delete();
        return redirect('/departmentDashboard');
    }

    public function updateDepartmentView($id)
    {
        $department = Departments::find($id);
        $departments = Departments::all();
        return view('departments.update', [
            'department' => $department,
            'departments' => $departments
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $departmentIds = $request->input('department_ids');
        if ($departmentIds) {
            Departments::whereIn('id', $departmentIds)->delete();
        }
        return redirect('/departmentDashboard');
    }
}
