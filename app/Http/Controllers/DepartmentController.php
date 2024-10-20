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
        // dd($department);
        $departments = Departments::all();
        return view('departments.update', [
            'department' => $department,
            'departments' => $departments
        ]);
    }

    public function updateDepartment(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        if ($request->parent_id == $request->id) {
            return redirect()->back()->withErrors(['parent_id' => 'Phòng ban không thể là cha của chính nó.']);
        }


        $updateData = [
            'name' => $validatedData['name'],
            'parent_id' => $validatedData['parent_id'],
        ];

        Departments::where('id', $id)->update($updateData);


        return redirect('/departmentDashboard');
    }

    public function bulkDelete(Request $request)
    {
        $departmentIds = $request->input('department_ids');
        if ($departmentIds) {
            Departments::whereIn('id', $departmentIds)->delete();
        }
        return redirect('/departmentDashboard');
    }

    public function details($id)
    {
        $department = Departments::with('users', 'children')->findOrFail($id);

        return view('departments.details')->with('department', $department);
    }
}
