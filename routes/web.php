<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\PDFController;


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


    Route::resource('subject', SubjectController::class);
});

    Route::get('generate-pdf', [PDFController::class, 'generatePDF']);
    Route::get('generate-image', [PDFController::class, 'generatePDFImage']);
