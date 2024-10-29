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
        // Validate dữ liệu
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Kiểm tra thông tin đăng nhập
        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();

            // Kiểm tra vai trò
            if ($user->role === 'admin') {
                return redirect()->intended('/dashboard'); // Điều hướng tới trang admin
            } elseif ($user->role === 'user' && $user->is_active === 1) {
                if ($user->is_department_active === 0) {
                    return back()->withErrors('Phòng ban bạn đã bị đình chỉ, hãy liên hệ với admin');
                }
                return redirect()->intended('/home'); // Điều hướng tới trang người dùng
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
