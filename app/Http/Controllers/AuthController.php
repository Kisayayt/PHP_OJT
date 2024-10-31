<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);


        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();


            if ($user->role === 'admin') {
                return redirect()->intended('/dashboard');
            } elseif ($user->role === 'user' && $user->is_active === 1) {
                if ($user->is_department_active === 0) {
                    return back()->withErrors('Phòng ban bạn đã bị đình chỉ, hãy liên hệ với admin');
                }
                if ($user->department_id == null) {
                    return back()->withErrors('Bạn hiện tại chưa nằm trong phòng ban nào để đăng nhập');
                }
                return redirect()->intended('/home');
            }
        }

        return back()->withErrors([
            'username' => 'Username hoặc mật khẩu không đúng.',
        ]);
    }



    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
