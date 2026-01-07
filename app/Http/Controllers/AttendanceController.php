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
        $date = $request->get('date') ?? '';
        $class_room = ClassRoom::all();
        if($date != ""){
            $date = $date;
        }
        else{
            $date = date('Y-m-d');
        }

        if($classId != ""){
            $class_id = $classId;
        }
        else{
            $class_id = $class_room[0]->id;
        }
        $students = Student::where('class_room_id', $class_id)->get();
        $attendanceData = [];
        if ($class_id && $date) {
            $attendanceRecords = Attendance::where('class_room_id', $class_id)
                ->where('date', $date)
                ->get();

            foreach ($attendanceRecords as $attendanceRecord) {
                $attendanceData[$attendanceRecord->student_id] = [
                    'status' => $attendanceRecord->attendance,
                ];
            }
        }

        if($classId != ""){
            $studentView = View::make('admin.pages.attendance.student_render_file', ['students' => $students, 'attendanceData' => $attendanceData])->render();
            return response()->json(['class_room' => $class_room, 'studentHtml' => $studentView]);
        } else{
            return view('admin.pages.attendance.index', compact('class_room', 'students', 'attendanceData'));
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
        $class_room = ClassRoom::all();
        return view('admin.pages.attendance.get_attendance_report', compact('class_room'));
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

   public function attendanceReport(Request $request)
    {
        $first_date = $request->first_date;
        $last_date = $request->last_date;
        $attendance = Attendance::with(['student', 'classRoom'])
            ->where('class_room_id', $request->class_id)
            ->whereBetween('date', [$request->first_date, $request->last_date])
            ->get()
            ->groupBy('student_id')
            ->map(function ($records) {

                return [
                    'student'   => $records->first()->student,
                    'classRoom' => $records->first()->classRoom,

                    'present' => $records->where('attendance', 1)->count(),
                    'leave'   => $records->where('attendance', 2)->count(),
                    'absent'  => $records->where('attendance', 3)->count(),
                ];
            });
            

           // dd($attendance);
        return view('admin.pages.attendance.attendance_report', compact('attendance', 'first_date', 'last_date'));
    }

}