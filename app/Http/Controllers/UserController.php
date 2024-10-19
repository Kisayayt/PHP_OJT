<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dashboard()
    {

        $users = User::with('department')->paginate(5);

        return view('dashboard.dashboard')->with('users', $users);
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
            'email' => 'required|email:dns|unique:users',
            'phone_number' => 'required|numeric|min:10',
            'password' => 'required|string|min:8|max:255|confirmed',
            'department_id' => 'required',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'password' => Hash::make($validatedData['password']),
            'department_id' => $validatedData['department_id'],
        ]);

        return redirect('/dashboard');
    }

    public function updateView($id)
    {
        $user = User::find($id);
        // dd($user);
        $departments = Departments::all();
        return view('dashboard.update', [
            'user' => $user,
            'departments' => $departments
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd($id);
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users,email,' . $id,
            'phone_number' => 'required|numeric|min:10',
            'password' => 'nullable|string|min:8|max:255|confirmed',
            'department_id' => 'required',
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


        User::where('id', $id)->update($updateData);

        return redirect('/dashboard');
    }
}
