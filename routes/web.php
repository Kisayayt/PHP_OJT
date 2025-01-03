<?php

use App\Http\Controllers\AdminCheckInOutController;
use App\Http\Controllers\AdminLeaveRequestController;
use App\Http\Controllers\AnotherChartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckInOutController;
use App\Http\Controllers\CheckInOutNotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\SalaryLevelController;
use App\Http\Controllers\WorkTimeController;
use App\Mail\CheckInOutNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return redirect()->route('login'); // Chuyển hướng đến trang login
})->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/test-mail', [CheckInOutNotificationController::class, 'testEmail'])->name('testEmail');











// USER
Route::middleware(['auth', 'checkRole:user'])->group(function () {
    // Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [CheckInOutController::class, 'showCheckInOut'])->name('checkinout');
    Route::post('/checkin', [CheckInOutController::class, 'checkIn'])->name('checkin');
    Route::post('/checkout', [CheckInOutController::class, 'checkOut'])->name('checkout');
    Route::get('/home/details', [HomeController::class, 'details'])->name('details');
    Route::post('/update-avatar', [HomeController::class, 'updateAvatar'])->name('update.avatar');
    Route::post('/leave-request', [LeaveRequestController::class, 'store'])->name('leave.request');

    // quan li don xin nghi
    Route::get('/leave-requests/view', [LeaveRequestController::class, 'index'])->name('leave_requests_user.index');
    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave_requests.create');
    // Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave_requests.store');
    Route::get('/leave-requests/{id}/edit', [LeaveRequestController::class, 'edit'])->name('leave_requests.edit');
    Route::put('/leave-requests/{id}', [LeaveRequestController::class, 'update'])->name('leave_requests.update');
    Route::delete('/leave-requests/{id}', [LeaveRequestController::class, 'destroy'])->name('leave_requests.destroy');


    Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change.password');

    Route::post('/submit-reason/{attendance}', [CheckInOutController::class, 'submitReason'])->name('submit-reason');
});

// ADMIN
Route::middleware(['auth', 'checkRole:admin'])->group(function () {
    // test
    Route::get('/departments/tree', [DepartmentController::class, 'getDepartmentTree']);

    Route::get('/run-payroll-calculate', function () {
        $exitCode = Artisan::call('payroll:calculate', ['--testTime' => '24:00:00']);
        return redirect()->back()->with('success', 'Tính lương cho tất cả nhân viên thành công!');
    })->name('run.payroll.calculate');

    //test

    Route::get('/create', [UserController::class, 'create']);
    Route::post('/insert', [UserController::class, 'insert']);
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
    Route::get('/dashboard/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/dashboard/{id}/details', [UserController::class, 'userDetails'])->name('userDetails');
    Route::put('/update-password/{id}', [UserController::class, 'updatePassword'])->name('user.updatePassword');



    Route::get('/update/{id}', [UserController::class, 'updateView'])->name('users.updateView');
    Route::put('/updated/{id}', [UserController::class, 'update']);

    Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
    Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');


    Route::get('/departmentDashboard', [DepartmentController::class, 'departmentDashboard'])->name('departments.index');
    Route::get('/departmentDashboard/search', [DepartmentController::class, 'search'])->name('departments.search');
    Route::get('/createDepartment', [DepartmentController::class, 'insertDepartmentView'])->name('departments.createView');
    Route::post('/insertDepartment', [DepartmentController::class, 'insertDepartment'])->name('departments.insertDepartment');

    Route::get('/updateDepartment/{id}', [DepartmentController::class, 'updateDepartmentView'])->name('departments.updateView');
    Route::put('/updatedDepartment/{id}', [DepartmentController::class, 'updateDepartment']);

    Route::delete('/deleteDepartment/{id}', [DepartmentController::class, 'deleteDepartment']);
    Route::delete('/departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('departments.bulkDelete');

    Route::get('/departmentDashboard/{id}/details', [DepartmentController::class, 'details'])->name('departments.details');


    Route::get('/departments/{id}/update-status', [DepartmentController::class, 'updateStatus'])->name('departments.updateStatus');

    Route::get('/checkinout', [AdminCheckInOutController::class, 'index'])->name('admin.checkinout');
    Route::get('checkinout/search', [AdminCheckInOutController::class, 'search'])->name('admin.checkinoutSearch');

    Route::get('/checkinout/filterByDate', [AdminCheckInOutController::class, 'filterByDate'])->name('admin.filterByDate');


    //export
    Route::get('/export-excel', [ExcelExportController::class, 'export'])->name('export');
    Route::get('/export-excel-all', [ExcelExportController::class, 'exportAll'])->name('export.all');
    // Route cho import
    Route::post('/import', [ExcelImportController::class, 'import'])->name('import');

    Route::get('/departmentDashboard/export-excel', [ExcelExportController::class, 'exportDepartment'])->name('exportDepartment');
    Route::get('/departmentDashboard/export-excel-all', [ExcelExportController::class, 'exportDepartmentAll'])->name('exportDepartmentAll');
    Route::post('/departmentDashboard/import-excel', [ExcelImportController::class, 'importDepartment'])->name('importDepartment');

    Route::get('/checkinout/export-excel', [ExcelExportController::class, 'exportCheck'])->name('exportCheck');
    Route::get('/payrolls/export', [ExcelExportController::class, 'exportPayrolls'])->name('payrolls.export');

    Route::get('/salaryLevels', [SalaryLevelController::class, 'index'])->name('salaryLevels');

    Route::get('/salaryLevels/create', [SalaryLevelController::class, 'create'])->name('salaryLevels.create');
    Route::post('/salaryLevels', [SalaryLevelController::class, 'store'])->name('salaryLevels.store');

    Route::get('/salaryLevels/{id}/edit', [SalaryLevelController::class, 'edit'])->name('salaryLevels.edit');
    Route::put('/salaryLevels/{id}', [SalaryLevelController::class, 'update'])->name('salaryLevels.update');

    Route::delete('/salaryLevels/soft-delete', [SalaryLevelController::class, 'softDeleteMultiple'])->name('salaryLevels.softDeleteMultiple');

    Route::get('/admin/requests', [AdminCheckInOutController::class, 'pendingRequests'])->name('admin.requests');
    Route::post('/admin/requests/{id}/accept', [AdminCheckInOutController::class, 'acceptRequest'])->name('admin.requests.accept');
    Route::post('/admin/requests/{id}/reject', [AdminCheckInOutController::class, 'rejectRequest'])->name('admin.requests.reject');

    Route::get('/work-time', [WorkTimeController::class, 'showWorkTime'])->name('admin.workTime');
    Route::post('/work-time/update', [WorkTimeController::class, 'updateWorkTime'])->name('admin.updateWorkTime');
    Route::post('/work-time/update-reminder', [WorkTimeController::class, 'updateReminder'])->name('admin.updateReminder');

    Route::get('/payroll/calculate', [PayrollController::class, 'showPayrollForm'])->name('payroll.form');
    Route::post('/payroll/calculate', [PayrollController::class, 'calculatePayroll'])->name('payroll.calculate');
    Route::post('/payroll/store', [PayrollController::class, 'storePayroll'])->name('payroll.store');

    Route::get('/requests-form/search', [AdminCheckInOutController::class, 'pendingRequests'])->name('admin.requests.search');


    Route::get('/payrolls', [PayrollController::class, 'showPayrolls'])->name('payrolls.index');
    Route::post('/send-reminders', [WorkTimeController::class, 'sendReminders'])
        ->name('send.reminders');


    Route::get('/charts-another', [AnotherChartController::class, 'index'])->name('charts.index');

    Route::get('/chart-view', [ChartController::class, 'chartView'])->name('chart.view');
    Route::get('/chart-view-test', [ChartController::class, 'view'])->name('view.view');
    Route::get('/api/total-employees', [ChartController::class, 'getTotalEmployees']);


    Route::get('/api/employee-ratio-by-department', [ChartController::class, 'getEmployeeRatioByDepartment']);
    Route::get('/api/age-gender-stats-by-department', [ChartController::class, 'getAgeGenderStatsByDepartment']);
    Route::get('/api/attendance-stats', [ChartController::class, 'getAttendanceStats']);
    Route::get('/api/contract-type-by-department', [ChartController::class, 'getContractTypeByDepartment']);
    Route::get('/api/gender-statistics', [ChartController::class, 'genderStatistics']);
    Route::get('/api/departments', [ChartController::class, 'getDepartments']);
    Route::get('/attendance-stats', [ChartController::class, 'getAttendanceStats']);



    Route::resource('reasons', ReasonController::class);
    Route::get('/reasons', [ReasonController::class, 'index'])->name('reasons.index');

    Route::get('/manage-leave_requests', [AdminLeaveRequestController::class, 'index'])->name('leave_requests.index');
    Route::put('/manage-leave_requests/{id}/status', [AdminLeaveRequestController::class, 'updateStatus'])->name('leave_requests.updateStatus');
});
