<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class UsersImport
{
    public function import($file)
    {
        $rows = Excel::load($file)->get();

        foreach ($rows as $row) {
            User::create([
                'name' => $row->name,
                'email' => $row->email,
                'password' => bcrypt($row->password),
            ]);
        }
    }
}
