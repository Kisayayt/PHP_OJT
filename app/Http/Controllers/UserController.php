<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\Departments;
use App\Models\SalaryLevel;
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

        $users = User::with('department')
            ->where('role', '<>', 'admin')
            ->Where('is_active', 1)
            ->paginate(5);


        return view('dashboard.dashboard')->with('users', $users);
    }

    public function userDetails($id)
    {
        $user = User::with('department', 'recentSalaryLevel')->findOrFail($id);

        $lastSalaryLevel = $user->recentSalaryLevel()->orderBy('created_at', 'desc')->first();

        $lastCheckIn = User_Attendance::where('user_id', $id)
            ->where('type', 'in')
            ->latest('created_at')
            ->first();

        $lastCheckOut = User_Attendance::where('user_id', $id)
            ->where('type', 'out')
            ->latest('created_at')
            ->first();

        return view('dashboard.details', compact('user', 'lastSalaryLevel', 'lastCheckIn', 'lastCheckOut'));
    }



    public function search(Request $request)
    {

        $search = $request->input('search');
        $users = User::with('department')
            ->where('role', 'user')
            ->where('is_active', 1)
            ->where('name', 'LIKE', "%{$search}%")
            ->paginate(3);

        return view('dashboard.dashboard', compact('users'));
    }

    public function create()
    {
        $departments = Departments::where('status', 1)->get();
        $salaryLevels = SalaryLevel::where('is_active', 1)->get();

        return view('dashboard.create', [
            'departments' => $departments,
            'salaryLevels' => $salaryLevels
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string|regex:/^\+(\d{2})\s?\d{9}$/',
            'password' => 'required|string|min:8|max:255|confirmed',
            'department_id' => 'required',
            'salary_level' => 'required|exists:salary_levels,id',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $phoneNumber = $request->input('phone_number');
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (strpos($phoneNumber, '+') === 0) {
            $phoneNumber = '0' . substr($phoneNumber, strpos($phoneNumber, '+') + 1);
        } elseif ($phoneNumber[0] !== '0') {
            $phoneNumber = '0' . $phoneNumber;
        }


        $avatarPath = 'images/defaultAvatar.jpg';
        if ($request->hasFile('avatar')) {
            $originalName = $request->file('avatar')->getClientOriginalName();
            $shortName = Str::limit($originalName, 50, '');
            $avatarPath = 'images/' . uniqid() . '_' . $shortName;
            $request->file('avatar')->move(public_path('images'), $avatarPath);
        }


        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'phone_number' => $phoneNumber,
            'password' => Hash::make($validatedData['password']),
            'department_id' => $validatedData['department_id'],
            'avatar' => $avatarPath,
        ]);


        $user->salaryLevels()->attach($validatedData['salary_level'], [
            'start_date' => now(),
            'end_date' => null,
        ]);

        return redirect('/dashboard')->with('success', 'Tạo người dùng mới thành công.');
    }






    public function updateView($id)
    {
        $user = User::find($id);

        $departments = Departments::where('status', 1)->get();
        $salaryLevels = SalaryLevel::where('is_active', 1)->get();
        return view('dashboard.update', [
            'user' => $user,
            'departments' => $departments,
            'salaryLevels' => $salaryLevels
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|max:255|unique:users,username,' . $id,
            'phone_number' => 'required|string',
            'salary_level' => 'required|exists:salary_levels,id',
            'password' => 'nullable|string|min:8|max:255|confirmed',
            'department_id' => 'nullable',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $phoneNumber = $request->input('phone_number');
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (strpos($phoneNumber, '+') === 0) {
            $phoneNumber = '0' . substr($phoneNumber, strpos($phoneNumber, '+') + 1);
        } elseif ($phoneNumber[0] !== '0') {
            $phoneNumber = '0' . $phoneNumber;
        }


        $updateData = [
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'phone_number' => $phoneNumber,
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


        $user = User::findOrFail($id);
        $user->update($updateData);


        $user->salaryLevels()
            ->wherePivot('end_date', null)
            ->update(['end_date' => now()]);


        $user->salaryLevels()->attach($validatedData['salary_level'], [
            'start_date' => now(),
            'end_date' => null,
        ]);

        return redirect('/dashboard')->with('success', 'Cập nhật thông tin người dùng thành công.');
    }


    public function updatePassword(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return redirect('/dashboard')->with('success', 'Mật khẩu đã được cập nhật thành công.');
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

            User::whereIn('id', $userIds)->update(['is_active' => 0]);
        }

        return redirect('/dashboard')->with('success', 'Người dùng đã được xóa thành công.');
    }
}
