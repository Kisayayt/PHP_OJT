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

        $users = User::with('department')->get();

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
}
