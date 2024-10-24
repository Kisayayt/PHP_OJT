<?php

namespace App\Http\Controllers;

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
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'password');
        $sheet->setCellValue('D1', 'email');
        $sheet->setCellValue('E1', 'phone_number');


        $data = [
            [9, 'Nguyễn Văn A', '12345678', 'a@example.com', '0876543345'],
            [10, 'Nguyễn Văn B', '12345678', 'b@example.com', '8657482929'],
        ];


        $row = 2;
        foreach ($data as $user) {
            $sheet->setCellValue('A' . $row, $user[0]);
            $sheet->setCellValue('B' . $row, $user[1]);
            $sheet->setCellValue('C' . $row, $user[2]);
            $sheet->setCellValue('D' . $row, $user[3]);
            $sheet->setCellValue('E' . $row, $user[4]);
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
}
