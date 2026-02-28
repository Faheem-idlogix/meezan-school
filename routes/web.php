<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ClassFeeVoucherController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('generate-pdf', [PDFController::class, 'generatePDF']);
Route::get('generate-image', [PDFController::class, 'generatePDFImage']);

Route::middleware(['auth'])->group(function () {

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('/', [HomeController::class, 'index']);

    // ===================== STUDENTS =====================
    Route::resource('student', StudentController::class);
    Route::post('student/{id}/restore', [StudentController::class, 'restore'])->name('student.restore');
    Route::delete('student/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('student.forceDelete');
    Route::get('/get-students-by-class', [StudentController::class, 'getStudentsByClass'])->name('getStudentsByClass');

    // ===================== TEACHERS =====================
    Route::resource('teacher', TeacherController::class);
    Route::post('teacher/{id}/restore', [TeacherController::class, 'restore'])->name('teacher.restore');
    Route::delete('teacher/{id}/force-delete', [TeacherController::class, 'forceDelete'])->name('teacher.forceDelete');

    // ===================== CLASSES =====================
    Route::post('class_update/{id}', [ClassRoomController::class, 'update'])->name('class_update');
    Route::post('class_destroy/{id}', [ClassRoomController::class, 'destroy'])->name('class_destroy');
    Route::get('class_edit/{id}', [ClassRoomController::class, 'edit'])->name('class_edit');
    Route::resource('class', ClassRoomController::class);
    Route::post('class/{id}/restore', [ClassRoomController::class, 'restore'])->name('class.restore');
    Route::delete('class/{id}/force-delete', [ClassRoomController::class, 'forceDelete'])->name('class.forceDelete');

    // ===================== SESSIONS =====================
    Route::resource('session', SessionController::class);

    // ===================== SUBJECTS =====================
    Route::resource('subject', SubjectController::class);
    Route::resource('class_subject', ClassSubjectController::class);

    // ===================== FEE & VOUCHERS =====================
    Route::get('fee_voucher', [ClassFeeVoucherController::class, 'index'])->name('fee_voucher');
    Route::get('fee_voucher_create', [ClassFeeVoucherController::class, 'create'])->name('fee_voucher_create');
    Route::post('store_fee_voucher', [ClassFeeVoucherController::class, 'store'])->name('store_fee_voucher');
    Route::delete('/voucher_destroy/{id}', [ClassFeeVoucherController::class, 'destroy'])->name('voucher_destroy');
    Route::get('generate_fee_invoice/{id}', [ClassFeeVoucherController::class, 'generate_fee_voucher'])->name('generate_fee_invoice');
    Route::get('class_fee/{id}', [StudentFeeController::class, 'index'])->name('class_fee');
    Route::get('student_fee_edit/{id}', [StudentFeeController::class, 'edit'])->name('student_fee_edit');
    Route::get('create_student_fee', [StudentFeeController::class, 'create'])->name('create_student_fee');
    Route::post('student_fee_updated/{id}', [StudentFeeController::class, 'update'])->name('student_fee_updated');
    Route::post('store_student_fee', [StudentFeeController::class, 'store'])->name('store_student_fee');
    Route::post('add_fee', [StudentFeeController::class, 'add_fee'])->name('add_fee');
    Route::post('edit_fee', [StudentFeeController::class, 'edit_fee'])->name('edit_fee');
    Route::post('add_full_fee', [StudentFeeController::class, 'add_full_fee'])->name('add_full_fee');
    Route::resource('voucher', VoucherController::class);

    // ===================== ATTENDANCE =====================
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('attendance_store', [AttendanceController::class, 'store'])->name('attendance_store');
    Route::get('attendance_report', [AttendanceController::class, 'attendanceReport'])->name('attendance_report');
    Route::get('get_attendance_report', [AttendanceController::class, 'show'])->name('get_attendance_report');

    // ===================== EXAMS =====================
    Route::resource('exam', ExamController::class);
    Route::resource('exam_result', ExamResultController::class);
    Route::get('exam_result/student/{studentId}/exam/{examId}', [ExamResultController::class, 'studentDetail'])
        ->name('exam_result.student_detail');
    Route::get('exam_result/ajax/class-data/{classId}', [ExamResultController::class, 'getClassData'])
        ->name('exam_result.class_data');

    // ===================== REPORTS =====================
    Route::get('result_card', [ReportController::class, 'result_card'])->name('result_card');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
    Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/students', [ReportController::class, 'students'])->name('reports.students');
    Route::get('reports/exams', [ReportController::class, 'exams'])->name('reports.exams');
    Route::get('reports/fees', [ReportController::class, 'fees'])->name('reports.fees');
    Route::get('reports/archived', [ReportController::class, 'archived'])->name('reports.archived');
    Route::get('documentation', [ReportController::class, 'documentation'])->name('documentation');

    // ===================== NOTICES =====================
    Route::resource('notice', NoticeController::class);
    Route::post('notice/{notice}/toggle-status', [NoticeController::class, 'toggleStatus'])->name('notice.toggleStatus');

    // ===================== USER MANAGEMENT =====================
    Route::resource('users', UserManagementController::class);
    Route::post('users/{id}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserManagementController::class, 'forceDelete'])->name('users.forceDelete');

    // ===================== SETTINGS & WHATSAPP =====================
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/send-whatsapp', [SettingController::class, 'sendWhatsApp'])->name('settings.sendWhatsApp');
    Route::post('settings/broadcast-notice', [SettingController::class, 'broadcastNotice'])->name('settings.broadcastNotice');

    // ===================== WHATSAPP HUB =====================
    Route::get('whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
    Route::post('whatsapp/sync-numbers', [WhatsAppController::class, 'syncNumbers'])->name('whatsapp.syncNumbers');
    Route::post('whatsapp/send-test', [WhatsAppController::class, 'sendTest'])->name('whatsapp.sendTest');
    Route::post('whatsapp/send-bulk', [WhatsAppController::class, 'sendBulk'])->name('whatsapp.sendBulk');
    Route::post('whatsapp/broadcast-notice', [WhatsAppController::class, 'broadcastNotice'])->name('whatsapp.broadcastNotice');
    Route::delete('whatsapp/clear-logs', [WhatsAppController::class, 'clearLogs'])->name('whatsapp.clearLogs');

    // ===================== FINANCE HUB =====================
    Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('finance/voucher', [FinanceController::class, 'storeVoucher'])->name('finance.voucher.store');
    Route::delete('finance/voucher/{voucher}', [FinanceController::class, 'destroyVoucher'])->name('finance.voucher.destroy');
    Route::get('finance/chart-data', [FinanceController::class, 'chartData'])->name('finance.chartData');
    Route::post('finance/fee-add-partial', [FinanceController::class, 'feeAddPartial'])->name('finance.feeAddPartial');

    // ===================== PAYROLL =====================
    Route::resource('payroll', PayrollController::class)->except(['edit', 'update']);
    Route::put('payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
    Route::put('payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payroll.markPaid');
    Route::get('payroll/{payroll}/payslip-pdf', [PayrollController::class, 'payslipPdf'])->name('payroll.payslip');
    Route::get('payroll-advances', [PayrollController::class, 'advances'])->name('payroll.advances');
    Route::post('payroll-advances', [PayrollController::class, 'storeAdvance'])->name('payroll.storeAdvance');

    // ===================== DAILY DIARY =====================
    Route::resource('diary', DiaryController::class);
    Route::post('diary/{diary}/whatsapp', [DiaryController::class, 'sendWhatsAppNow'])->name('diary.whatsapp');

    // ===================== TIMETABLE =====================
    Route::resource('timetable', TimetableController::class)->only(['index', 'create', 'store', 'destroy']);

    // ===================== LEAVE MANAGEMENT =====================
    Route::resource('leave', LeaveController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::put('leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::put('leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');

    // ===================== ACTIVITY LOGS =====================
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity_logs.show');
    Route::delete('activity-logs', [ActivityLogController::class, 'destroy'])->name('activity_logs.destroy');

    // ===================== SUPER ADMIN =====================
    Route::prefix('super-admin')->name('super_admin.')->middleware('super_admin')->group(function () {
        Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('schools', [SuperAdminController::class, 'schools'])->name('schools');
        Route::get('schools/create', [SuperAdminController::class, 'createSchool'])->name('schools.create');
        Route::post('schools', [SuperAdminController::class, 'storeSchool'])->name('schools.store');
        Route::get('schools/{school}/edit', [SuperAdminController::class, 'editSchool'])->name('schools.edit');
        Route::put('schools/{school}', [SuperAdminController::class, 'updateSchool'])->name('schools.update');
        Route::delete('schools/{school}', [SuperAdminController::class, 'destroySchool'])->name('schools.destroy');
        Route::get('plans', [SuperAdminController::class, 'plans'])->name('plans');
        Route::post('plans', [SuperAdminController::class, 'storePlan'])->name('plans.store');
    });
});
