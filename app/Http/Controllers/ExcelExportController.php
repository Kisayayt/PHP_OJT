<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\User;
use App\Models\User_Attendance;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportController extends Controller
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', 'department_id');
        $sheet->setCellValue('B1', 'username');
        $sheet->setCellValue('C1', 'name');
        $sheet->setCellValue('D1', 'password');
        $sheet->setCellValue('E1', 'email');
        $sheet->setCellValue('F1', 'phone_number');


        $data = [
            [9, 'nguyenvana', 'Nguyễn Văn A', '12345678', 'a@example.com', '0876543345'],
            [10, 'nguyenvanb', 'Nguyễn Văn B', '12345678', 'b@example.com', '8657482929'],
        ];

        $row = 2;
        foreach ($data as $user) {
            $sheet->setCellValue('A' . $row, $user[0]);
            $sheet->setCellValue('B' . $row, $user[1]);
            $sheet->setCellValue('C' . $row, $user[2]);
            $sheet->setCellValue('D' . $row, $user[3]);
            $sheet->setCellValue('E' . $row, $user[4]);
            $sheet->setCellValue('F' . $row, $user[5]);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $fileName = 'users.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    public function exportDepartment()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'parent_id');
        $data = [
            ['Phòng cứu hộ', '9'],
            ['Phòng bệnh', '10'],
        ];


        $row = 2;
        foreach ($data as $user) {
            $sheet->setCellValue('A' . $row, $user[0]);
            $sheet->setCellValue('B' . $row, $user[1]);
            $row++;
        }


        $writer = new Xlsx($spreadsheet);


        $fileName = 'Departments.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');


        $writer->save('php://output');
        exit;
    }

    public function exportCheck(Request $request)
    {

        $attendanceRecords = User_Attendance::with('user')->get();


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Nhân viên');
        $sheet->setCellValue('C1', 'Thời gian');
        $sheet->setCellValue('D1', 'Ngày/Tháng/Năm');
        $sheet->setCellValue('E1', 'Tổng thời gian (giờ)');
        $sheet->setCellValue('F1', 'Trạng thái');


        $rowNumber = 2;
        foreach ($attendanceRecords as $record) {
            $sheet->setCellValue('A' . $rowNumber, $record->user->id);
            $sheet->setCellValue('B' . $rowNumber, $record->user->name);
            $sheet->setCellValue('C' . $rowNumber, $record->created_at->format('H:i'));
            $sheet->setCellValue('D' . $rowNumber, $record->created_at->format('d/m/Y'));
            $sheet->setCellValue('E' . $rowNumber, $record->type === 'in' ? '--' : $record->time . ' giờ');
            $sheet->setCellValue('F' . $rowNumber, $record->type === 'in' ? 'Đang check-in' : 'Đã check-out');

            $rowNumber++;
        }


        $writer = new Xlsx($spreadsheet);


        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });


        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="user_attendances.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }


    public function exportAll()
    {

        $users = User::with('department')->where('role', 'user')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Ảnh');
        $sheet->setCellValue('C1', 'Tên');
        $sheet->setCellValue('D1', 'Username');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'SĐT');
        $sheet->setCellValue('G1', 'Phòng ban');
        $sheet->setCellValue('H1', 'Ngày cập nhật');

        $rowNumber = 2;


        foreach ($users as $user) {
            $sheet->setCellValue('A' . $rowNumber, $user->id);
            $sheet->setCellValue('B' . $rowNumber, $user->avatar);
            $sheet->setCellValue('C' . $rowNumber, $user->name);
            $sheet->setCellValue('D' . $rowNumber, $user->username);
            $sheet->setCellValue('E' . $rowNumber, $user->email);
            $sheet->setCellValue('F' . $rowNumber, $user->phone_number);
            $sheet->setCellValue('G' . $rowNumber, $user->department ? $user->department->name : 'N/A');
            $sheet->setCellValue('H' . $rowNumber, $user->updated_at->format('d/m/Y'));

            $rowNumber++;
        }


        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });


        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="all_users.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }


    public function exportDepartmentAll()
    {

        $departments = Departments::with('parent')->get();


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Tên');
        $sheet->setCellValue('C1', 'Phòng ban cha');
        $sheet->setCellValue('D1', 'Trạng thái');
        $sheet->setCellValue('E1', 'Ngày cập nhật');

        $rowNumber = 2;


        foreach ($departments as $department) {
            $sheet->setCellValue('A' . $rowNumber, $department->id);
            $sheet->setCellValue('B' . $rowNumber, $department->name);
            $sheet->setCellValue('C' . $rowNumber, $department->parent ? $department->parent->name : 'Không có');
            $sheet->setCellValue('D' . $rowNumber, $department->status ? 'Hoạt động' : 'Đình chỉ');
            $sheet->setCellValue('E' . $rowNumber, $department->updated_at->format('d/m/Y'));

            $rowNumber++;
        }


        $writer = new Xlsx($spreadsheet);


        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });


        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="departments.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
