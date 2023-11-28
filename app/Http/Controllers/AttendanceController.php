<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Support\Facades\View;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request  )
    {
        $classId = $request->get('classId') ?? '';
        $class_room = ClassRoom::all();
        if($classId != ""){
            $class_id = $classId;
        }
        else{
            $class_id = $class_room[0]->id;
        }
        $students = Student::where('class_room_id', $class_id)->get();
        if($classId != ""){
            $studentView = View::make('admin.pages.attendance.student_render_file', ['students' => $students])->render();
            return response()->json(['class_room' => $class_room, 'studentHtml' => $studentView]);
        } else{
            return view('admin.pages.attendance.index', compact('class_room', 'students'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $studentId = $request->studentId;
        $selectedClassId = $request->selectedClassId;
        $date = $request->date;
        $attendance = $request->attendance;
        $storeAttendance = Attendance::where('student_id', $studentId)->where('class_room_id', $selectedClassId)->where('date', $date)->first();
        if($storeAttendance){
            $storeAttendance->student_id = $studentId;
            $storeAttendance->class_room_id = $selectedClassId;
            $storeAttendance->date = $date;
            $storeAttendance->attendance = $attendance;
            $storeAttendance->save();
        } else{
            $storeAttendance = new Attendance();
            $storeAttendance->student_id = $studentId;
            $storeAttendance->class_room_id = $selectedClassId;
            $storeAttendance->date = $date;
            $storeAttendance->attendance = $attendance;
            $storeAttendance->save();
        }

        return response()->json(['attendance'=> $storeAttendance]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
