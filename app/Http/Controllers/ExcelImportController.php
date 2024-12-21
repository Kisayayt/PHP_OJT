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
            return redirect()->back()->withErrors(['Vui lòng chọn tệp để nhập!']);
        }

        $spreadsheet = IOFactory::load($request->file('import_file')->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $failedImports = [];
        foreach (array_slice($rows, 1) as $rowIndex => $row) {
            $errors = [];

            $departmentId = $row[0];
            if (!Departments::where('id', $departmentId)->exists()) {
                $errors[] = 'Phòng ban không tồn tại';
            }

            $username = $row[1];
            if (User::where('username', $username)->exists()) {
                $errors[] = 'Tên tài khoản đã tồn tại';
            }

            $email = $row[4];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            } elseif (User::where('email', $email)->exists()) {
                $errors[] = 'Email đã tồn tại';
            }

            $phoneNumber = $row[5];
            if (!preg_match('/^\+84 \d{9}$/', $phoneNumber)) {
                $errors[] = 'Số điện thoại không hợp lệ';
            } else {
                // Chuyển đổi số điện thoại từ +84 thành 0
                $phoneNumber = '0' . substr($phoneNumber, 4); // Bỏ +84 và thay thế bằng 0
            }


            $gender = strtolower(trim($row[6]));
            if ($gender === 'nam') {
                $gender = 'male';
            } elseif ($gender === 'nữ') {
                $gender = 'female';
            } else {
                $errors[] = 'Giới tính không hợp lệ (chỉ chấp nhận Nam hoặc Nữ)';
            }

            $systemRole = strtolower(trim($row[8]));
            if ($systemRole !== 'admin' && $systemRole !== 'user') {
                $errors[] = 'Vai trò hệ thống không hợp lệ (chỉ chấp nhận admin hoặc user)';
            }

            $employeeRole = strtolower(trim($row[9]));
            if ($employeeRole !== 'part_time' && $employeeRole !== 'official') {
                $errors[] = 'Vai trò nhân viên không hợp lệ (chỉ chấp nhận part_time hoặc official)';
            }

            if (!empty($errors)) {
                $failedImports[] = [
                    'row' => $rowIndex + 2,
                    'errors' => $errors,
                ];
                continue;
            }

            $validatedData = [
                'department_id' => $departmentId,
                'username' => $username,
                'name' => $row[2],
                'password' => bcrypt($row[3]),
                'email' => $email,
                'phone_number' => $phoneNumber,
                'avatar' => '/images/defaultAvatar.jpg',
                'role' => $systemRole,
                'employee_role' => $employeeRole,
                'gender' => $gender,
                'age' => $row[7] ?? null,
            ];

            User::create($validatedData);
        }

        if (count($failedImports) > 0) {
            $errorMessages = [];
            foreach ($failedImports as $failedImport) {
                $errorMessages[] = "Hàng " . $failedImport['row'] . ": " . implode(', ', $failedImport['errors']);
            }
            return redirect()->back()->withErrors($errorMessages);
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
