<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\Departments;
use App\Models\User;
use App\Models\User_Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class UserController extends Controller
{
    public function dashboard()
    {

        $users = User::with('department')->where('role', '<>', 'admin')->paginate(3);

        return view('dashboard.dashboard')->with('users', $users);
    }

    public function search(Request $request)
    {

        $search = $request->input('search');
        $users = User::with('department')
            ->where('role', 'user')
            ->where('name', 'LIKE', "%{$search}%")
            ->paginate(3);

        return view('dashboard.dashboard', compact('users'));
    }

    public function create()
    {
        $departments = Departments::all();
        return view('dashboard.create', [
            'departments' => $departments
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|numeric|min:10',
            'password' => 'required|string|min:8|max:255|confirmed',
            'department_id' => 'required',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $originalName = $request->file('avatar')->getClientOriginalName();
            $shortName = Str::limit($originalName, 50, '');
            $avatarPath = 'images/' . uniqid() . '_' . $shortName;
            $request->file('avatar')->move(public_path('images'), $avatarPath);
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'password' => Hash::make($validatedData['password']),
            'department_id' => $validatedData['department_id'],
            'avatar' => $avatarPath,
        ]);

        return redirect('/dashboard');
    }



    public function updateView($id)
    {
        $user = User::find($id);

        $departments = Departments::all();
        return view('dashboard.update', [
            'user' => $user,
            'departments' => $departments
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|numeric|min:10',
            'password' => 'nullable|string|min:8|max:255|confirmed',
            'department_id' => '',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'department_id' => $validatedData['department_id'],
        ];


        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }


        if ($request->hasFile('avatar')) {
            $originalName = $request->file('avatar')->getClientOriginalName();
            $shortName = Str::limit($originalName, 50, '');
            $avatarPath = 'images/' . uniqid() . '_' . $shortName;
            $request->file('avatar')->move(public_path('images'), $avatarPath);

            $updateData['avatar'] = $avatarPath;
        }


        User::where('id', $id)->update($updateData);

        return redirect('/dashboard')->with('success', 'Cập nhật thông tin người dùng thành công.');
    }

    public function deleteUser($id)
    {

        User::where('id', $id)->delete();
        return redirect('/dashboard');
    }

    public function bulkDelete(Request $request)
    {
        $userIds = $request->input('user_ids');

        if ($userIds) {

            User_Attendance::whereIn('user_id', $userIds)->delete();


            User::whereIn('id', $userIds)->delete();
        }

        return redirect('/dashboard')->with('success', 'Người dùng đã được xóa thành công.');
    }
}
