<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {

        $users = User::all();

        return view('dashboard.dashboard')->with('users', $users);
    }

    public function create()
    {

        return view('dashboard.create');
    }
}
