<?php

namespace App\Http\Controllers;

use App\Models\User_Attendance;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $userHasCheckedIn = User_Attendance::where('user_id', $userId)
            ->where('type', 'in')
            ->whereNull('updated_at') // Nếu chưa checkout
            ->exists();

        return view('UserHome.index', compact('userHasCheckedIn'));
    }
}
