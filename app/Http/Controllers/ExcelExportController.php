<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;

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
}
