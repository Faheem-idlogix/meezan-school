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






/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('test', function () {
    return view('admin.pages.student.student_detail');
});
Route::get('check', function () {
    return view('pdfImage');
});
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('/', [HomeController::class, 'index']);
    Route::post('class_update/{id}', [ClassRoomController::class, 'update'])->name('class_update');
    Route::post('class_destroy/{id}', [ClassRoomController::class, 'destroy'])->name('class_destroy');

    Route::get('class_edit/{id}', [ClassRoomController::class, 'edit'])->name('class_edit');
    Route::resource('student', StudentController::class);
    Route::resource('session', SessionController::class);

    Route::resource('class', ClassRoomController::class);

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
    Route::get('/get-students-by-class', [StudentController::class, 'getStudentsByClass'])->name('getStudentsByClass');
    Route::post('add_fee', [StudentFeeController::class, 'add_fee'])->name('add_fee');
    Route::post('edit_fee', [StudentFeeController::class, 'edit_fee'])->name('edit_fee');
    Route::post('add_full_fee', [StudentFeeController::class, 'add_full_fee'])->name('add_full_fee');

    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('attendance_store', [AttendanceController::class, 'store'])->name('attendance_store');
    Route::get('attendance_report', [AttendanceController::class, 'attendanceReport'])->name('attendance_report');
    Route::get('get_attendance_report', [AttendanceController::class, 'show'])->name('get_attendance_report');

    Route::resource('subject', SubjectController::class);

    Route::resource('class_subject', ClassSubjectController::class);

    Route::resource('exam', ExamController::class);

    Route::resource('exam_result', ExamResultController::class);

    Route::resource('voucher', VoucherController::class);
    
    // AJAX endpoint for fetching class data
    Route::get('exam_result/ajax/class-data/{classId}', [ExamResultController::class, 'getClassData'])
        ->name('exam_result.class_data');

    





    // Route::prefix('subject')->group(function () {
    //     Route::get('/', [SubjectController::class, 'index'])->name('subject');
    //     Route::get('/create', [SubjectController::class, 'create'])->name('subject.create');
    //     Route::post('/store', [SubjectController::class, 'store'])->name('subject.store');
    //     Route::get('/edit/{subject}', [SubjectController::class, 'edit'])->name('subject.edit');
    //     Route::delete('/subject/{subject}', [SubjectController::class, 'destroy'])->name('subject.destroy');
    // });


    Route::get('result_card', [ReportController::class, 'result_card'])->name('result_card');


    // Route::get('/run-migrations', function () {
    //     Artisan::call('migrate', ['--force' => true]);
    //     return 'Migrations completed successfully.';
    // });

    // Route::get('/clear-config', function () {
    //     Artisan::call('config:clear');
    //     return 'Configuration cache cleared successfully.';
    // });
    
    // Route::get('/clear-view', function () {
    //     Artisan::call('view:clear');
    //     return 'View cache cleared successfully.';
    // });




});

    Route::get('generate-pdf', [PDFController::class, 'generatePDF']);
    Route::get('generate-image', [PDFController::class, 'generatePDFImage']);
