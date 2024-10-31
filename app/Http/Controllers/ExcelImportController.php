<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\User;
use App\Models\Departments;

class ExcelImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls',
        ]);

        if ($request->file('import_file') === null) {
            return redirect()->back()->with('errors', 'Vui lòng chọn tệp để nhập!');
        }

        $spreadsheet = IOFactory::load($request->file('import_file')->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $failedImports = [];
        foreach (array_slice($rows, 1) as $row) {
            $departmentId = $row[0];


            if (!Departments::where('id', $departmentId)->exists()) {
                $failedImports[] = [
                    'row' => $row,
                    'errors' => 'department_id không tồn tại'
                ];
                continue;
            }

            $username = $row[1];
            $email = $row[4];


            if (User::where('email', $email)->exists()) {
                $failedImports[] = [
                    'row' => $row,
                    'errors' => 'Email đã tồn tại'
                ];
                continue;
            }

            $validatedData = [
                'department_id' => $departmentId,
                'username' => $username,
                'name' => $row[2],
                'password' => bcrypt($row[3]),
                'email' => $email,
                'phone_number' => $row[5],
                'avatar' => '/images/defaultAvatar.jpg',
                'role' => 'user',
            ];

            User::create($validatedData);
        }

        if (count($failedImports) > 0) {

            return redirect()->back()->withErrors($failedImports);
        }

        return redirect()->back()->with('success', 'Dữ liệu đã được import thành công!');
    }


    public function importDepartment(Request $request)
    {

        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls',
        ]);

        if ($request->file('import_file') === null) {
            return redirect()->back()->with('errors', 'Vui lòng chọn tệp để nhập!');
        }


        $spreadsheet = IOFactory::load($request->file('import_file')->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $failedImports = [];

        foreach (array_slice($rows, 1) as $row) {
            $name = $row[0];
            $parentId = $row[1];
            $status = 1;


            if (Departments::where('name', $name)->exists()) {
                $failedImports[] = [
                    'row' => $row,
                    'errors' => 'Tên phòng ban đã tồn tại'
                ];
                continue;
            }


            if (!is_null($parentId) && !Departments::where('id', $parentId)->exists()) {
                $failedImports[] = [
                    'row' => $row,
                    'errors' => 'Parent department không tồn tại'
                ];
                continue;
            }


            $validatedData = [
                'name' => $name,
                'parent_id' => $parentId,
                'status' => $status,
            ];


            Departments::create($validatedData);
        }


        if (count($failedImports) > 0) {
            return redirect()->back()->with('error', 'Một số dòng không được nhập do lỗi.');
        }


        return redirect()->back()->with('success', 'Dữ liệu đã được import thành công!');
    }
}
