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
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\StudentBehaviorController;
use App\Http\Controllers\TransferCertificateController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeeDiscountController;
use App\Http\Controllers\FeeInstallmentController;
use App\Http\Controllers\LateFeeRuleController;
use App\Http\Controllers\GradingSystemController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\VoucherStatusController;
use App\Http\Controllers\SystemErrorLogController;

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
    Route::middleware('permission:students.view')->group(function () {
        Route::resource('student', StudentController::class);
        Route::post('student/{id}/restore', [StudentController::class, 'restore'])->name('student.restore');
        Route::delete('student/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('student.forceDelete');
        Route::get('/get-students-by-class', [StudentController::class, 'getStudentsByClass'])->name('getStudentsByClass');
    });

    // ===================== TEACHERS =====================
    Route::middleware('permission:teachers.view')->group(function () {
        Route::resource('teacher', TeacherController::class);
        Route::post('teacher/{id}/restore', [TeacherController::class, 'restore'])->name('teacher.restore');
        Route::delete('teacher/{id}/force-delete', [TeacherController::class, 'forceDelete'])->name('teacher.forceDelete');
    });

    // ===================== CLASSES =====================
    Route::middleware('permission:classes.view')->group(function () {
        Route::post('class_update/{id}', [ClassRoomController::class, 'update'])->name('class_update');
        Route::post('class_destroy/{id}', [ClassRoomController::class, 'destroy'])->name('class_destroy');
        Route::get('class_edit/{id}', [ClassRoomController::class, 'edit'])->name('class_edit');
        Route::resource('class', ClassRoomController::class);
        Route::post('class/{id}/restore', [ClassRoomController::class, 'restore'])->name('class.restore');
        Route::delete('class/{id}/force-delete', [ClassRoomController::class, 'forceDelete'])->name('class.forceDelete');
    });

    // ===================== SESSIONS =====================
    Route::middleware('permission:sessions.view')->group(function () {
        Route::resource('session', SessionController::class);
    });

    // ===================== SUBJECTS =====================
    Route::middleware('permission:subjects.view')->group(function () {
        Route::resource('subject', SubjectController::class);
        Route::resource('class_subject', ClassSubjectController::class);
    });

    // ===================== FEE & VOUCHERS =====================
    Route::middleware('permission:fees.view')->group(function () {
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
    });

    // ===================== ATTENDANCE =====================
    Route::middleware('permission:attendance.view')->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance');
        Route::post('attendance_store', [AttendanceController::class, 'store'])->name('attendance_store');
        Route::get('attendance_report', [AttendanceController::class, 'attendanceReport'])->name('attendance_report');
        Route::get('get_attendance_report', [AttendanceController::class, 'show'])->name('get_attendance_report');
        Route::get('attendance/dashboard-stats', [AttendanceController::class, 'dashboardStats'])->name('attendance.dashboard-stats');
        Route::get('attendance/class-students', [AttendanceController::class, 'classStudents'])->name('attendance.class-students');
    });

    // ===================== EXAMS =====================
    Route::middleware('permission:exams.view')->group(function () {
        Route::resource('exam', ExamController::class);
        Route::resource('exam_result', ExamResultController::class);
        Route::get('exam_result/student/{studentId}/exam/{examId}', [ExamResultController::class, 'studentDetail'])
            ->name('exam_result.student_detail');
        Route::get('exam_result/ajax/class-data/{classId}', [ExamResultController::class, 'getClassData'])
            ->name('exam_result.class_data');
    });

    // ===================== REPORTS =====================
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('result_card', [ReportController::class, 'result_card'])->name('result_card');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
        Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('reports/students', [ReportController::class, 'students'])->name('reports.students');
        Route::get('reports/exams', [ReportController::class, 'exams'])->name('reports.exams');
        Route::get('reports/fees', [ReportController::class, 'fees'])->name('reports.fees');
        Route::get('reports/archived', [ReportController::class, 'archived'])->name('reports.archived');
    });
    Route::get('documentation', [ReportController::class, 'documentation'])->name('documentation');

    // ===================== NOTICES =====================
    Route::middleware('permission:notices.view')->group(function () {
        Route::resource('notice', NoticeController::class);
        Route::post('notice/{notice}/toggle-status', [NoticeController::class, 'toggleStatus'])->name('notice.toggleStatus');
    });

    // ===================== USER MANAGEMENT =====================
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::post('users/{id}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [UserManagementController::class, 'forceDelete'])->name('users.forceDelete');
    });

    // ===================== ROLES & PERMISSIONS =====================
    Route::middleware('permission:roles.view')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // ===================== SETTINGS & WHATSAPP =====================
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/send-whatsapp', [SettingController::class, 'sendWhatsApp'])->name('settings.sendWhatsApp');
        Route::post('settings/broadcast-notice', [SettingController::class, 'broadcastNotice'])->name('settings.broadcastNotice');
    });

    // ===================== WHATSAPP HUB =====================
    Route::middleware('permission:whatsapp.view')->group(function () {
        Route::get('whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
        Route::post('whatsapp/sync-numbers', [WhatsAppController::class, 'syncNumbers'])->name('whatsapp.syncNumbers');
        Route::post('whatsapp/send-test', [WhatsAppController::class, 'sendTest'])->name('whatsapp.sendTest');
        Route::post('whatsapp/send-bulk', [WhatsAppController::class, 'sendBulk'])->name('whatsapp.sendBulk');
        Route::post('whatsapp/broadcast-notice', [WhatsAppController::class, 'broadcastNotice'])->name('whatsapp.broadcastNotice');
        Route::delete('whatsapp/clear-logs', [WhatsAppController::class, 'clearLogs'])->name('whatsapp.clearLogs');
    });

    // ===================== DATABASE BACKUP =====================
    Route::middleware('permission:database_backup.view')->group(function () {
        Route::get('backup', [DatabaseBackupController::class, 'index'])->name('backup.index');
        Route::post('backup/create', [DatabaseBackupController::class, 'create'])->name('backup.create');
        Route::get('backup/download/{filename}', [DatabaseBackupController::class, 'download'])->name('backup.download');
        Route::post('backup/email', [DatabaseBackupController::class, 'email'])->name('backup.email');
        Route::delete('backup/{filename}', [DatabaseBackupController::class, 'destroy'])->name('backup.destroy');
        Route::post('backup/mail-settings', [DatabaseBackupController::class, 'saveMailSettings'])->name('backup.saveMailSettings');
        Route::post('backup/test-mail', [DatabaseBackupController::class, 'testMail'])->name('backup.testMail');
    });

    // ===================== FINANCE HUB =====================
    Route::middleware('permission:finance.view')->group(function () {
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('finance/voucher', [FinanceController::class, 'storeVoucher'])->name('finance.voucher.store');
        Route::delete('finance/voucher/{voucher}', [FinanceController::class, 'destroyVoucher'])->name('finance.voucher.destroy');
        Route::get('finance/chart-data', [FinanceController::class, 'chartData'])->name('finance.chartData');
        Route::post('finance/fee-add-partial', [FinanceController::class, 'feeAddPartial'])->name('finance.feeAddPartial');
    });

    // ===================== PAYROLL =====================
    Route::middleware('permission:payroll.view')->group(function () {
        Route::resource('payroll', PayrollController::class)->except(['edit', 'update']);
        Route::put('payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
        Route::put('payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payroll.markPaid');
        Route::get('payroll/{payroll}/payslip-pdf', [PayrollController::class, 'payslipPdf'])->name('payroll.payslip');
        Route::get('payroll-advances', [PayrollController::class, 'advances'])->name('payroll.advances');
        Route::post('payroll-advances', [PayrollController::class, 'storeAdvance'])->name('payroll.storeAdvance');
    });

    // ===================== DAILY DIARY =====================
    Route::middleware('permission:diary.view')->group(function () {
        Route::resource('diary', DiaryController::class);
        Route::post('diary/{diary}/whatsapp', [DiaryController::class, 'sendWhatsAppNow'])->name('diary.whatsapp');
    });

    // ===================== TIMETABLE =====================
    Route::middleware('permission:timetable.view')->group(function () {
        Route::resource('timetable', TimetableController::class)->only(['index', 'create', 'store', 'destroy']);
    });

    // ===================== LEAVE MANAGEMENT =====================
    Route::middleware('permission:leave.view')->group(function () {
        Route::resource('leave', LeaveController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::put('leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
        Route::put('leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');
    });

    // ===================== ACTIVITY LOGS =====================
    Route::middleware('permission:activity_logs.view')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
        Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity_logs.show');
        Route::delete('activity-logs', [ActivityLogController::class, 'destroy'])->name('activity_logs.destroy');
    });

    // ===================== ERROR LOGS =====================
    Route::middleware('permission:error_logs.view')->group(function () {
        Route::get('error-logs', [SystemErrorLogController::class, 'index'])->name('error-logs.index');
        Route::get('error-logs/{errorLog}', [SystemErrorLogController::class, 'show'])->name('error-logs.show');
        Route::delete('error-logs', [SystemErrorLogController::class, 'destroy'])->name('error-logs.destroy');
    });

    // ===================== VOUCHER STATUS =====================
    Route::middleware('permission:fees.view')->group(function () {
        Route::get('voucher-status', [VoucherStatusController::class, 'index'])->name('voucher-status.index');
        Route::get('voucher-status/export', [VoucherStatusController::class, 'export'])->name('voucher-status.export');
    });

    // ===================== ADMISSION / ENQUIRY =====================
    Route::middleware('permission:admission.view')->group(function () {
        Route::resource('admission', AdmissionController::class);
        Route::post('admission/{admission}/schedule-test', [AdmissionController::class, 'scheduleTest'])->name('admission.scheduleTest');
        Route::post('admission/{admission}/record-test', [AdmissionController::class, 'recordTestResult'])->name('admission.recordTest');
        Route::post('admission/{admission}/approve', [AdmissionController::class, 'approve'])->name('admission.approve');
        Route::post('admission/{admission}/reject', [AdmissionController::class, 'reject'])->name('admission.reject');
        Route::post('admission/{admission}/enroll', [AdmissionController::class, 'enroll'])->name('admission.enroll');
    });

    // ===================== STUDENT DOCUMENTS =====================
    Route::middleware('permission:students.view')->group(function () {
        Route::get('student/{student}/documents', [StudentDocumentController::class, 'index'])->name('student.documents');
        Route::post('student/{student}/documents', [StudentDocumentController::class, 'store'])->name('student.documents.store');
        Route::post('student-document/{studentDocument}/verify', [StudentDocumentController::class, 'verify'])->name('student.documents.verify');
        Route::delete('student-document/{studentDocument}', [StudentDocumentController::class, 'destroy'])->name('student.documents.destroy');
    });

    // ===================== STUDENT BEHAVIOR =====================
    Route::middleware('permission:behavior.view')->group(function () {
        Route::resource('behavior', StudentBehaviorController::class);
    });

    // ===================== TRANSFER CERTIFICATES =====================
    Route::middleware('permission:transfer_certificates.view')->group(function () {
        Route::resource('transfer-certificate', TransferCertificateController::class)->except(['edit', 'update']);
        Route::post('transfer-certificate/{transfer_certificate}/issue', [TransferCertificateController::class, 'issue'])->name('transfer-certificate.issue');
        Route::get('transfer-certificate/{transfer_certificate}/pdf', [TransferCertificateController::class, 'pdf'])->name('transfer-certificate.pdf');
    });

    // ===================== ALUMNI =====================
    Route::middleware('permission:alumni.view')->group(function () {
        Route::resource('alumni', AlumniController::class);
    });

    // ===================== FEE STRUCTURE =====================
    Route::middleware('permission:fee_structure.view')->group(function () {
        Route::resource('fee-structures', FeeStructureController::class);
        Route::get('fee-structures/class-fees/{classRoomId}', [FeeStructureController::class, 'getClassFees'])->name('fee-structures.class-fees');
    });

    // ===================== FEE DISCOUNTS =====================
    Route::middleware('permission:fee_discounts.view')->group(function () {
        Route::resource('fee-discounts', FeeDiscountController::class);
        Route::post('fee-discounts/{fee_discount}/assign-student', [FeeDiscountController::class, 'assignStudent'])->name('fee-discounts.assign-student');
        Route::post('fee-discounts/{studentFeeDiscount}/remove-student', [FeeDiscountController::class, 'removeStudent'])->name('fee-discounts.remove-student');
    });

    // ===================== FEE INSTALLMENTS =====================
    Route::middleware('permission:fee_installments.view')->group(function () {
        Route::resource('fee-installments', FeeInstallmentController::class)->except(['edit', 'update']);
        Route::post('fee-installment/{feeInstallment}/record-payment', [FeeInstallmentController::class, 'recordPayment'])->name('fee-installments.record-payment');
    });

    // ===================== LATE FEE RULES =====================
    Route::middleware('permission:late_fee.view')->group(function () {
        Route::resource('late-fee-rules', LateFeeRuleController::class)->except(['create', 'show', 'edit']);
    });

    // ===================== GRADING SYSTEMS =====================
    Route::middleware('permission:grading.view')->group(function () {
        Route::resource('grading-systems', GradingSystemController::class);
    });

    // ===================== EXAM SCHEDULES =====================
    Route::middleware('permission:exam_schedules.view')->group(function () {
        Route::resource('exam-schedules', ExamScheduleController::class)->except(['show', 'edit', 'update']);
    });

    // ===================== REPORT CARDS =====================
    Route::middleware('permission:report_cards.view')->group(function () {
        Route::get('report-cards/config', [ReportCardController::class, 'config'])->name('report-cards.config');
        Route::post('report-cards/config', [ReportCardController::class, 'storeConfig'])->name('report-cards.store-config');
        Route::get('report-cards/generate', [ReportCardController::class, 'generate'])->name('report-cards.generate');
        Route::get('report-cards/pdf', [ReportCardController::class, 'pdf'])->name('report-cards.pdf');
        Route::post('report-cards/calculate-grades', [ReportCardController::class, 'calculateGrades'])->name('report-cards.calculate-grades');
        Route::post('report-cards/approve-results', [ReportCardController::class, 'approveResults'])->name('report-cards.approve-results');
    });

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

    // ===================== NOTIFICATIONS =====================
    // User-facing routes (any authenticated user) — MUST be before resource/parameterized routes
    Route::get('notifications/my', [NotificationController::class, 'myNotifications'])->name('notifications.my');
    Route::get('notifications/navbar-data', [NotificationController::class, 'navbarData'])->name('notifications.navbar-data');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');

    // Admin notification management
    Route::middleware('permission:notifications.view')->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications', [NotificationController::class, 'store'])->name('notifications.store');
        Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    // ===================== PROFILE =====================
    Route::get('profile', [NotificationController::class, 'showProfile'])->name('profile.index');
    Route::put('profile', [NotificationController::class, 'updateProfile'])->name('profile.update');
    Route::get('profile/change-password', [NotificationController::class, 'showChangePassword'])->name('profile.change-password');
    Route::post('profile/change-password', [NotificationController::class, 'updatePassword'])->name('profile.update-password');

    // ===================== SKIN / APPEARANCE =====================
    Route::get('appearance', function () {
        return view('admin.pages.appearance.index');
    })->name('appearance.index');

    Route::post('user/skin', function (\Illuminate\Http\Request $request) {
        $request->validate(['skin' => 'required|string|in:cyber,glass,luxury,minimal,ocean,rose']);
        $request->user()->update(['skin' => $request->skin]);
        return response()->json(['ok' => true, 'skin' => $request->skin]);
    })->name('user.skin');

    // ===================== GLOBAL SEARCH =====================
    Route::get('search', [GlobalSearchController::class, 'index'])->name('global.search');
    Route::get('search/suggest', [GlobalSearchController::class, 'suggest'])->name('global.search.suggest');
});
