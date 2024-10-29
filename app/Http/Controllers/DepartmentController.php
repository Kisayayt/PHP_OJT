<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function departmentDashboard()
    {
        $departments = Departments::with('parent')
            ->where('is_active', 1)
            ->paginate(5);

        return view('departments.index')->with('departments', $departments);
    }

    public function search(Request $request)
    {

        $search = $request->input('search');

        $departments = Departments::with('parent')
            ->where('name', 'LIKE', "%{$search}%")
            ->paginate(5);

        return view('departments.index', compact('departments'));
    }

    public function getDepartmentTree()
    {
        $departments = Departments::with('children')->get();
        return response()->json($departments);
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
        $users = User::where('department_id', $id)->get();

        foreach ($users as $user) {
            $user->department_id = null;
            $user->save();
        }

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

    public function updateDepartment(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
        ]);
        if ($request->parent_id == $id) {
            return redirect()->back()->withErrors(['parent_id' => 'Phòng ban không thể là cha của chính nó.']);
        }
        $subDepartments = Departments::where('parent_id', $id)->pluck('id')->toArray();
        $subDepartmentsLevel2 = Departments::whereIn('parent_id', $subDepartments)->pluck('id')->toArray();
        $allSubDepartments = array_merge($subDepartments, $subDepartmentsLevel2);

        if (in_array($request->parent_id, $allSubDepartments)) {
            return redirect()->back()->withErrors(['parent_id' => 'Phòng ban không thể thuộc vào một trong các phòng ban con của chính nó.']);
        }

        $updateData = [
            'name' => $validatedData['name'],
            'parent_id' => $validatedData['parent_id'],
        ];

        Departments::where('id', $id)->update($updateData);

        return redirect('/departmentDashboard')->with('success', 'Cập nhật phòng ban thành công.');
    }



    public function bulkDelete(Request $request)
    {
        $departmentIds = $request->input('department_ids');

        if ($departmentIds) {

            User::whereIn('department_id', $departmentIds)->update(['department_id' => null]);

            Departments::whereIn('parent_id', $departmentIds)->update(['parent_id' => null]);


            Departments::whereIn('id', $departmentIds)->update(['is_active' => 0]);
        }

        return redirect('/departmentDashboard')->with('success', 'Phòng ban đã được xóa mềm thành công.');
    }



    public function details($id)
    {
        $department = Departments::with('users', 'children')->findOrFail($id);

        return view('departments.details')->with('department', $department);
    }

    public function updateStatus($id)
    {
        $department = Departments::findOrFail($id);

        // Chuyển đổi trạng thái của phòng ban
        $department->status = $department->status ? 0 : 1;
        $department->save();

        if ($department->status == 0) {
            // Khi phòng ban bị đình chỉ, cập nhật user và phòng ban con thành không hoạt động
            $users = User::where('department_id', $department->id)->get();
            foreach ($users as $user) {
                $user->is_department_active = 0;
                $user->save();
            }

            $subDepartments = Departments::where('parent_id', $department->id)->get();
            foreach ($subDepartments as $subDepartment) {
                $subDepartment->status = 0;
                $subDepartment->save();

                $subDepartmentUsers = User::where('department_id', $subDepartment->id)->get();
                foreach ($subDepartmentUsers as $subUser) {
                    $subUser->is_department_active = 0;
                    $subUser->save();
                }
            }
        } else {
            // Khi phòng ban chuyển sang hoạt động, cập nhật user và phòng ban con thành hoạt động
            $users = User::where('department_id', $department->id)->get();
            foreach ($users as $user) {
                $user->is_department_active = 1;
                $user->save();
            }

            $subDepartments = Departments::where('parent_id', $department->id)->get();
            foreach ($subDepartments as $subDepartment) {
                $subDepartment->status = 1;
                $subDepartment->save();

                $subDepartmentUsers = User::where('department_id', $subDepartment->id)->get();
                foreach ($subDepartmentUsers as $subUser) {
                    $subUser->is_department_active = 1;
                    $subUser->save();
                }
            }
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái phòng ban thành công');
    }
}
