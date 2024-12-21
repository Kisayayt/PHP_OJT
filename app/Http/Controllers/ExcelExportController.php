<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\Payroll;
use App\Models\User;
use App\Models\User_Attendance;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;


class ExcelExportController extends Controller
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cấu hình tiêu đề cho các cột
        $sheet->setCellValue('A1', 'department_id');
        $sheet->setCellValue('B1', 'username');
        $sheet->setCellValue('C1', 'name');
        $sheet->setCellValue('D1', 'password');
        $sheet->setCellValue('E1', 'email');
        $sheet->setCellValue('F1', 'phone_number');
        $sheet->setCellValue('G1', 'gender');
        $sheet->setCellValue('H1', 'age');
        $sheet->setCellValue('I1', 'role'); // admin / user
        $sheet->setCellValue('J1', 'employee_role'); // official / part_time

        // Dữ liệu mẫu (có thể lấy từ database)
        $data = [
            [9, 'nguyenvana', 'Nguyễn Văn A', '12345678', 'a@example.com', '+84 987654321', 'Nam', 25, 'admin', 'official'],
            [10, 'nguyenvanb', 'Nguyễn Văn B', '12345678', 'b@example.com', '+84 912345678', 'Nữ', 30, 'user', 'part_time'],
        ];

        // Ghi dữ liệu vào file Excel
        $row = 2;
        foreach ($data as $user) {
            $sheet->setCellValue('A' . $row, $user[0]);
            $sheet->setCellValue('B' . $row, $user[1]);
            $sheet->setCellValue('C' . $row, $user[2]);
            $sheet->setCellValue('D' . $row, $user[3]);
            $sheet->setCellValue('E' . $row, $user[4]);
            $sheet->setCellValue('F' . $row, $user[5]);
            $sheet->setCellValue('G' . $row, $user[6]);
            $sheet->setCellValue('H' . $row, $user[7]);
            $sheet->setCellValue('I' . $row, $user[8]); // admin / user
            $sheet->setCellValue('J' . $row, $user[9]); // official / part_time
            $row++;
        }

        // Xuất file Excel
        $writer = new Xlsx($spreadsheet);

        $fileName = 'user_template.xlsx';
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
        $batchSize = 5;


        $tempDir = storage_path('app/temp_exports/');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunks = $attendanceRecords->chunk($batchSize);


        $filePaths = [];
        foreach ($chunks as $index => $chunk) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();


            $sheet->setCellValue('A1', '#');
            $sheet->setCellValue('B1', 'Nhân viên');
            $sheet->setCellValue('C1', 'Thời gian');
            $sheet->setCellValue('D1', 'Ngày/Tháng/Năm');
            $sheet->setCellValue('E1', 'Tổng thời gian (giờ)');
            $sheet->setCellValue('F1', 'Trạng thái');

            $rowNumber = 2;
            foreach ($chunk as $record) {
                $sheet->setCellValue('A' . $rowNumber, $record->user->id);
                $sheet->setCellValue('B' . $rowNumber, $record->user->name);
                $sheet->setCellValue('C' . $rowNumber, $record->created_at->format('H:i'));
                $sheet->setCellValue('D' . $rowNumber, $record->created_at->format('d/m/Y'));
                $sheet->setCellValue('E' . $rowNumber, $record->type === 'in' ? '--' : $record->time . ' giờ');
                $sheet->setCellValue('F' . $rowNumber, $record->type === 'in' ? 'Đang check-in' : 'Đã check-out');
                $rowNumber++;
            }


            $filePath = $tempDir . 'attendance_batch_' . ($index + 1) . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            $filePaths[] = $filePath;
        }

        $zipFilePath = storage_path('app/temp_exports/attendance_records.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($filePaths as $filePath) {
                $zip->addFile($filePath, basename($filePath));
            }
            $zip->close();
        }


        foreach ($filePaths as $filePath) {
            unlink($filePath);
        }


        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }



    public function exportAll()
    {
        $users = User::with('department')
            ->where('role', 'user')
            ->where('is_active', 1)
            ->get();

        $batchSize = 5;

        $tempDir = storage_path('app/temp_exports/');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunks = $users->chunk($batchSize);

        $filePaths = [];
        foreach ($chunks as $index => $chunk) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Thêm các cột tiêu đề mới
            $sheet->setCellValue('A1', '#');
            $sheet->setCellValue('B1', 'Ảnh');
            $sheet->setCellValue('C1', 'Tên');
            $sheet->setCellValue('D1', 'Username');
            $sheet->setCellValue('E1', 'Email');
            $sheet->setCellValue('F1', 'SĐT');
            $sheet->setCellValue('G1', 'Phòng ban');
            $sheet->setCellValue('H1', 'Ngày cập nhật');
            $sheet->setCellValue('I1', 'Giới tính');  // Cột giới tính
            $sheet->setCellValue('J1', 'Tuổi');      // Cột tuổi
            $sheet->setCellValue('K1', 'Vai trò nhân viên'); // Cột vai trò nhân viên

            $rowNumber = 2;
            foreach ($chunk as $user) {
                $sheet->setCellValue('A' . $rowNumber, $user->id);
                $sheet->setCellValue('B' . $rowNumber, $user->avatar);
                $sheet->setCellValue('C' . $rowNumber, $user->name);
                $sheet->setCellValue('D' . $rowNumber, $user->username);
                $sheet->setCellValue('E' . $rowNumber, $user->email);
                $sheet->setCellValue('F' . $rowNumber, $user->phone_number);
                $sheet->setCellValue('G' . $rowNumber, $user->department ? $user->department->name : 'N/A');
                $sheet->setCellValue('H' . $rowNumber, $user->updated_at->format('d/m/Y'));

                // Thay đổi giá trị giới tính sang tiếng Việt
                $gender = $user->gender == 'male' ? 'Nam' : ($user->gender == 'female' ? 'Nữ' : '');

                // Thay đổi vai trò nhân viên sang tiếng Việt
                $employeeRole = $user->employee_role == 'official' ? 'Chính thức' : ($user->employee_role == 'part_time' ? 'Bán thời gian' : '');

                // Điền dữ liệu cho các cột mới
                $sheet->setCellValue('I' . $rowNumber, $gender);  // Giới tính
                $sheet->setCellValue('J' . $rowNumber, $user->age);     // Tuổi
                $sheet->setCellValue('K' . $rowNumber, $employeeRole); // Vai trò nhân viên

                $rowNumber++;
            }

            $filePath = $tempDir . 'user_batch_' . ($index + 1) . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            $filePaths[] = $filePath;
        }

        $zipFilePath = storage_path('app/temp_exports/users.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($filePaths as $filePath) {
                $zip->addFile($filePath, basename($filePath));
            }
            $zip->close();
        }

        // Xóa các file tạm sau khi đã thêm vào zip
        foreach ($filePaths as $filePath) {
            unlink($filePath);
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }






    public function exportDepartmentAll()
    {
        $departments = Departments::with('parent')->where('is_active', 1)->get();

        $batchSize = 5;


        $tempDir = storage_path('app/temp_exports/');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunks = $departments->chunk($batchSize);


        $filePaths = [];
        foreach ($chunks as $index => $chunk) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();


            $sheet->setCellValue('A1', '#');
            $sheet->setCellValue('B1', 'Tên');
            $sheet->setCellValue('C1', 'Phòng ban cha');
            $sheet->setCellValue('D1', 'Trạng thái');
            $sheet->setCellValue('E1', 'Ngày cập nhật');

            $rowNumber = 2;
            foreach ($chunk as $department) {
                $sheet->setCellValue('A' . $rowNumber, $department->id);
                $sheet->setCellValue('B' . $rowNumber, $department->name);
                $sheet->setCellValue('C' . $rowNumber, $department->parent ? $department->parent->name : 'Không có');
                $sheet->setCellValue('D' . $rowNumber, $department->status ? 'Hoạt động' : 'Đình chỉ');
                $sheet->setCellValue('E' . $rowNumber, $department->updated_at->format('d/m/Y'));

                $rowNumber++;
            }


            $filePath = $tempDir . 'department_batch_' . ($index + 1) . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            $filePaths[] = $filePath;
        }


        $zipFilePath = storage_path('app/temp_exports/departments.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($filePaths as $filePath) {
                $zip->addFile($filePath, basename($filePath));
            }
            $zip->close();
        }


        foreach ($filePaths as $filePath) {
            unlink($filePath);
        }


        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }


    public function exportPayrolls(Request $request)
    {
        $payrolls = Payroll::with('user')->get();

        // Số bản ghi mỗi file Excel
        $batchSize = 5;

        // Tạo thư mục tạm để lưu các file Excel
        $tempDir = storage_path('app/temp_exports/');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Phân chia bản ghi thành các nhóm
        $chunks = $payrolls->chunk($batchSize);

        // Tạo file Excel cho mỗi nhóm
        $filePaths = [];
        foreach ($chunks as $index => $chunk) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tiêu đề của các cột
            $sheet->setCellValue('A1', '#');
            $sheet->setCellValue('B1', 'Nhân viên');
            $sheet->setCellValue('C1', 'Hệ số lương');
            $sheet->setCellValue('D1', 'Số ngày công hợp lệ');
            $sheet->setCellValue('E1', 'Số ngày công không hợp lệ');
            $sheet->setCellValue('F1', 'Lương nhận được');
            $sheet->setCellValue('G1', 'Ngày tính lương');

            $rowNumber = 2;
            foreach ($chunk as $payroll) {
                $sheet->setCellValue('A' . $rowNumber, $payroll->id);
                $sheet->setCellValue('B' . $rowNumber, $payroll->user->name);
                $sheet->setCellValue('C' . $rowNumber, $payroll->salary_coefficient);
                $sheet->setCellValue('D' . $rowNumber, $payroll->valid_days);
                $sheet->setCellValue('E' . $rowNumber, $payroll->invalid_days);
                $sheet->setCellValue('F' . $rowNumber, number_format($payroll->salary_received, 0));
                $sheet->setCellValue('G' . $rowNumber, $payroll->created_at->format('d/m/Y'));

                $rowNumber++;
            }

            // Lưu mỗi file Excel vào thư mục tạm
            $filePath = $tempDir . 'payroll_batch_' . ($index + 1) . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            $filePaths[] = $filePath;
        }

        // Tạo file ZIP để nén các file Excel
        $zipFilePath = storage_path('app/temp_exports/payrolls.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($filePaths as $filePath) {
                $zip->addFile($filePath, basename($filePath)); // Thêm mỗi file Excel vào ZIP
            }
            $zip->close();
        }

        // Xóa các file Excel tạm
        foreach ($filePaths as $filePath) {
            unlink($filePath);
        }

        // Trả về file ZIP để người dùng tải về
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
