<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ClassFeeVoucherController;
use App\Http\Controllers\AttendanceController;





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
Auth::routes();

Route::middleware(['auth'])->group(function () {
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
    Route::get('generate_fee_invoice/{id}', [ClassFeeVoucherController::class, 'generate_fee_voucher'])->name('generate_fee_invoice');

    Route::get('class_fee/{id}', [StudentFeeController::class, 'index'])->name('class_fee');
    Route::get('student_fee_edit/{id}', [StudentFeeController::class, 'edit'])->name('student_fee_edit');
    Route::post('student_fee_updated/{id}', [StudentFeeController::class, 'update'])->name('student_fee_updated');
    Route::post('add_fee', [StudentFeeController::class, 'add_fee'])->name('add_fee');
    Route::post('edit_fee', [StudentFeeController::class, 'edit_fee'])->name('edit_fee');
    Route::post('add_full_fee', [StudentFeeController::class, 'add_full_fee'])->name('add_full_fee');

    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('attendance_store', [AttendanceController::class, 'store'])->name('attendance_store');

    Route::get('/run-migrations', function () {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migrations completed successfully.';
    });

    Route::get('/clear-config', function () {
        Artisan::call('config:clear');
        return 'Configuration cache cleared successfully.';
    });
    
    Route::get('/clear-view', function () {
        Artisan::call('view:clear');
        return 'View cache cleared successfully.';
    });




    Route::resource('subject', SubjectController::class);
});

    Route::get('generate-pdf', [PDFController::class, 'generatePDF']);
    Route::get('generate-image', [PDFController::class, 'generatePDFImage']);
