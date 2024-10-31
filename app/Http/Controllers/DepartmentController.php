<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function departmentDashboard()
    {
        $departments = Departments::whereNull('parent_id')
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
        $departments = Departments::where('status', 1)->get();

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


        $allSubDepartments = $this->getAllSubDepartments($id);


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



    private function getAllSubDepartments($departmentId)
    {
        $subDepartments = Departments::where('parent_id', $departmentId)->pluck('id')->toArray();
        $allSubDepartments = $subDepartments;

        foreach ($subDepartments as $subId) {
            $allSubDepartments = array_merge($allSubDepartments, $this->getAllSubDepartments($subId));
        }

        return $allSubDepartments;
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


        $department->status = $department->status ? 0 : 1;
        $department->save();


        $this->updateUsersStatus($department->id, $department->status);


        $this->updateSubDepartmentsStatus($department->id, $department->status);

        return redirect()->back()->with('success', 'Cập nhật trạng thái phòng ban thành công');
    }

    /**
     * Hàm đệ quy để cập nhật trạng thái cho phòng ban con và user của chúng.
     */
    private function updateSubDepartmentsStatus($parentId, $status)
    {

        $subDepartments = Departments::where('parent_id', $parentId)->get();
        foreach ($subDepartments as $subDepartment) {
            $subDepartment->status = $status;
            $subDepartment->save();


            $this->updateUsersStatus($subDepartment->id, $status);


            $this->updateSubDepartmentsStatus($subDepartment->id, $status);
        }
    }

    /**
     * Cập nhật trạng thái cho tất cả user thuộc phòng ban.
     */
    private function updateUsersStatus($departmentId, $status)
    {
        $users = User::where('department_id', $departmentId)->get();
        foreach ($users as $user) {
            $user->is_department_active = $status;
            $user->save();
        }
    }
}
