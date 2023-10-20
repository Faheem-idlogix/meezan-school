<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentFee;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalStudents = Student::count();
        $classrooms = ClassRoom::count();
        $currentMonth = date('Y-m');
        $totalFee = StudentFee::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])->sum('total_fee');
        $students = StudentFee::with('student')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])->get();
        return view('admin.dashboard.dashboard', compact('totalStudents', 'classrooms', 'totalFee', 'students'));
    }
}
