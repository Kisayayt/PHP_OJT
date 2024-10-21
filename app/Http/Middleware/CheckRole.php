<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check() || Auth::user()->role != $role) {
            // Chuyển hướng người dùng không có quyền
            return redirect('/login')->with('error', 'Bạn không có quyền truy cập trang này.');
        }
        return $next($request);
    }
}
