<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\User_Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class HomeController extends Controller
{
    // public function index()
    // {
    //     $userId = auth()->id();
    //     $userHasCheckedIn = User_Attendance::where('user_id', $userId)
    //         ->where('type', 'in')
    //         ->whereNull('updated_at') // Nếu chưa checkout
    //         ->exists();

    //     return view('UserHome.index', compact('userHasCheckedIn'));
    // }


    public function details()
    {
        $user = auth()->user();
        $userId = auth()->id();
        $latestAttendance = User_Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        $latestCheckout = User_Attendance::where('user_id', $userId)
            ->where('type', 'out')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastCheckoutTime = $latestCheckout ? $latestCheckout->time : 0;

        $isCheckedIn = $latestAttendance && $latestAttendance->type == 'in';
        $time = $latestAttendance ? $latestAttendance->time : 0;
        return view('userHome.details', compact('user', 'isCheckedIn', 'time', 'lastCheckoutTime'));
    }

    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);


        if (!Hash::check($validatedData['current_password'], Auth::user()->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }


        $user = User::find(Auth::id());
        $user->update(['password' => Hash::make($validatedData['new_password'])]);

        return redirect()->back()->with('success', 'Mật khẩu đã được đổi thành công.');
    }

    public function updateAvatar(Request $request)
    {

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Kích thước tối đa là 2MB
        ]);

        $updateData = [];


        if ($request->hasFile('avatar')) {
            try {
                $originalName = $request->file('avatar')->getClientOriginalName();
                $shortName = Str::limit($originalName, 50, '');
                $avatarPath = 'images/' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $shortName);
                $request->file('avatar')->move(public_path('images'), $avatarPath);

                $updateData['avatar'] = $avatarPath;
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['avatar' => 'Có lỗi xảy ra khi tải ảnh lên: ']);
            }
        }


        $user = User::find(Auth::id());
        $user->update($updateData);

        return redirect()->back();
    }
}
