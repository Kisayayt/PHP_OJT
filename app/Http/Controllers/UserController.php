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
        // Validate input data
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string|regex:/^\+(\d{2})\s?\d{9}$/',
            'password' => 'required|string|min:8|max:255|confirmed',
            'department_id' => 'required',
            'salary_level' => 'required|exists:salary_levels,id',
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|in:male,female',
            'employee_role' => 'required|in:official,part_time',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Process phone number
        $phoneNumber = $request->input('phone_number');
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (strpos($phoneNumber, '+') === 0) {
            $phoneNumber = '0' . substr($phoneNumber, strpos($phoneNumber, '+') + 1);
        } elseif ($phoneNumber[0] !== '0') {
            $phoneNumber = '0' . $phoneNumber;
        }

        // Handle avatar upload
        $avatarPath = 'images/defaultAvatar.jpg';
        if ($request->hasFile('avatar')) {
            $originalName = $request->file('avatar')->getClientOriginalName();
            $shortName = Str::limit($originalName, 50, '');
            $avatarPath = 'images/' . uniqid() . '_' . $shortName;
            $request->file('avatar')->move(public_path('images'), $avatarPath);
        }

        // Default leave balance (adjust based on role)
        $defaultLeaveBalance = $validatedData['employee_role'] === 'official' ? 12 : 6;

        // Create new user
        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'phone_number' => $phoneNumber,
            'password' => Hash::make($validatedData['password']),
            'department_id' => $validatedData['department_id'],
            'age' => $validatedData['age'],
            'gender' => $validatedData['gender'],
            'employee_role' => $validatedData['employee_role'],
            'avatar' => $avatarPath,
            'leave_balance' => $defaultLeaveBalance,
        ]);

        // Attach salary level with start date
        $user->salaryLevels()->attach($validatedData['salary_level'], [
            'start_date' => now(),
            'end_date' => null,
        ]);

        // Redirect with success message
        return redirect('/dashboard')->with('success', 'Tạo người dùng mới thành công.');
    }








    public function updateView($id)
    {
        $user = User::findOrFail($id);
        $departments = Departments::where('status', 1)->get();
        $salaryLevels = SalaryLevel::where('is_active', 1)->get();

        // Lấy salary_level hiện tại
        $currentSalaryLevel = $user->currentSalaryLevel();

        // Chuyển đổi số điện thoại sang định dạng +84 nếu bắt đầu bằng '0'
        if (substr($user->phone_number, 0, 1) === '0') {
            $user->phone_number = '+84 ' . substr($user->phone_number, 1);
        }

        return view('dashboard.update', [
            'user' => $user,
            'departments' => $departments,
            'salaryLevels' => $salaryLevels,
            'currentSalaryLevel' => $currentSalaryLevel, // Truyền vào view
        ]);
    }


    public function update(Request $request, $id)
    {
        // dd($request->all());
        // Validate input data
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|max:255|unique:users,username,' . $id,
            'phone_number' => 'required|string|regex:/^\+(\d{2})\s?\d{9}$/',  // Validating the phone number format
            'salary_level' => 'required|exists:salary_levels,id',
            'password' => 'nullable|string|min:8|max:255|confirmed',  // Password is optional but must meet criteria if provided
            'department_id' => 'nullable',
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|in:male,female',
            'employee_role' => 'required|in:official,part_time',  // Employee role must be either 'official' or 'part_time'
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Process phone number
        $phoneNumber = $request->input('phone_number');
        $phoneNumber = str_replace(' ', '', $phoneNumber);  // Remove any spaces from phone number

        // If phone number starts with '+84', convert it to '0'
        if (strpos($phoneNumber, '+84') === 0) {
            $phoneNumber = '0' . substr($phoneNumber, 3);  // Replace '+84' with '0'
        } elseif ($phoneNumber[0] !== '0') {
            // If phone number does not start with '0', prepend '0'
            $phoneNumber = '0' . $phoneNumber;
        }

        // Now validate again after processing the phone number
        $validatedData['phone_number'] = $phoneNumber; // Ensure the processed phone number is validated

        // Prepare data for updating
        $updateData = [
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'phone_number' => $phoneNumber,
            'department_id' => $validatedData['department_id'],
            'age' => $validatedData['age'],
            'gender' => $validatedData['gender'],
            'employee_role' => $validatedData['employee_role'],
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $originalName = $request->file('avatar')->getClientOriginalName();
            $shortName = Str::limit($originalName, 50, '');  // Shorten file name to prevent long file names
            $avatarPath = 'images/' . uniqid() . '_' . $shortName;  // Create unique path for image
            $request->file('avatar')->move(public_path('images'), $avatarPath);  // Move file to the public directory

            $updateData['avatar'] = $avatarPath;
        }

        // Update user information
        $user = User::findOrFail($id);
        $user->update($updateData);

        // Update salary level (end current one and attach new one)
        $user->salaryLevels()
            ->wherePivot('end_date', null)
            ->update(['end_date' => now()]);  // Mark current salary level as ended

        $user->salaryLevels()->attach($validatedData['salary_level'], [
            'start_date' => now(),
            'end_date' => null,
        ]);

        // Adjust leave balance based on role
        $defaultLeaveBalance = $validatedData['employee_role'] === 'official' ? 12 : 6;  // 12 days for official, 6 for part-time
        $user->update(['leave_balance' => $defaultLeaveBalance]);

        // Redirect back with success message
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
