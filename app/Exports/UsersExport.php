<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Excel;

class UsersExport
{
    public function export()
    {
        $users = User::all(); // Lấy toàn bộ dữ liệu của bảng users

        // Định dạng dữ liệu để xuất ra Excel
        $output = [];

        foreach ($users as $user) {
            $output[] = [
                'ID'     => $user->id,
                'Name'   => $user->name,
                'Email'  => $user->email,
            ];
        }

        // Trả về dữ liệu dưới dạng mảng
        return $output;
    }
}
