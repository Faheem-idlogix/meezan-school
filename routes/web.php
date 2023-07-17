<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SessionController;

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

Route::get('home', [HomeController::class, 'index'])->name('home');
Route::resource('Student', StudentController::class,);
Route::resource('Session', SessionController::class,);
Route::resource('Class', ClassRoomController::class,);
Route::resource('Subject', SubjectController::class,);