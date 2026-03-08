<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

    /**
     * AJAX: Dashboard attendance stats filtered by date range.
     */
    public function dashboardStats(Request $request)
    {
        [$dateFrom, $dateTo, $label] = $this->resolveFilter($request->get('filter', 'today'));

        $totalStudents = Student::whereNull('deleted_at')->count();

        // Overall stats
        $raw = Attendance::whereBetween('date', [$dateFrom, $dateTo])
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN attendance = '1' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance = '3' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance = '2' THEN 1 ELSE 0 END) as on_leave,
                SUM(CASE WHEN attendance = '0' THEN 1 ELSE 0 END) as late
            ")->first();

        $total   = (int)($raw->total ?? 0);
        $present = (int)($raw->present ?? 0);
        $absent  = (int)($raw->absent ?? 0);
        $leave   = (int)($raw->on_leave ?? 0);
        $late    = (int)($raw->late ?? 0);
        $rate    = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        // For single-day filters, unmarked = total students - marked students
        $isSingleDay = ($dateFrom === $dateTo);
        $unmarked = $isSingleDay ? max(0, $totalStudents - $total) : 0;

        // Class-wise breakdown
        $classData = Attendance::whereBetween('date', [$dateFrom, $dateTo])
            ->join('class_rooms', 'attendances.class_room_id', '=', 'class_rooms.id')
            ->selectRaw("
                class_rooms.id as class_id,
                class_rooms.class_name,
                class_rooms.section_name,
                COUNT(*) as total,
                SUM(CASE WHEN attendance = '1' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance = '3' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance = '2' THEN 1 ELSE 0 END) as on_leave,
                SUM(CASE WHEN attendance = '0' THEN 1 ELSE 0 END) as late
            ")
            ->groupBy('class_rooms.id', 'class_rooms.class_name', 'class_rooms.section_name')
            ->orderBy('class_rooms.class_name')
            ->get();

        return response()->json([
            'label'    => $label,
            'total'    => $total,
            'present'  => $present,
            'absent'   => $absent,
            'leave'    => $leave,
            'late'     => $late,
            'unmarked' => $unmarked,
            'rate'     => $rate,
            'classes'  => $classData,
        ]);
    }

    /**
     * AJAX: Student-level attendance for a specific class + filter.
     */
    public function classStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'filter'   => 'required|string',
        ]);

        [$dateFrom, $dateTo, $label] = $this->resolveFilter($request->get('filter'));

        $classRoom = ClassRoom::findOrFail($request->class_id);

        // Get all students in the class
        $students = Student::where('class_room_id', $request->class_id)
            ->whereNull('deleted_at')
            ->orderBy('student_name')
            ->get();

        // Get attendance records grouped by student
        $records = Attendance::where('class_room_id', $request->class_id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get()
            ->groupBy('student_id');

        $isSingleDay = ($dateFrom === $dateTo);
        $totalDays = $isSingleDay ? 1 : (int)Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;

        $data = $students->map(function ($student) use ($records, $isSingleDay, $totalDays) {
            $stuRecords = $records->get($student->id, collect());
            $present = $stuRecords->where('attendance', '1')->count();
            $absent  = $stuRecords->where('attendance', '3')->count();
            $leave   = $stuRecords->where('attendance', '2')->count();
            $late    = $stuRecords->where('attendance', '0')->count();
            $marked  = $stuRecords->count();
            $rate    = $marked > 0 ? round(($present / $marked) * 100, 1) : 0;

            // Single-day status
            $status = 'unmarked';
            if ($isSingleDay && $stuRecords->isNotEmpty()) {
                $val = $stuRecords->first()->attendance;
                $status = match($val) {
                    '1' => 'present', '2' => 'leave', '3' => 'absent', '0' => 'late', default => 'unmarked'
                };
            }

            return [
                'id'       => $student->id,
                'name'     => $student->student_name,
                'father'   => $student->father_name,
                'present'  => $present,
                'absent'   => $absent,
                'leave'    => $leave,
                'late'     => $late,
                'rate'     => $rate,
                'status'   => $status,
                'is_single'=> $isSingleDay,
            ];
        });

        return response()->json([
            'class_name' => $classRoom->class_name . ($classRoom->section_name ? ' - ' . $classRoom->section_name : ''),
            'label'      => $label,
            'total_days' => $totalDays,
            'students'   => $data,
        ]);
    }

    /**
     * Resolve filter string to date range.
     */
    private function resolveFilter(string $filter): array
    {
        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        return match($filter) {
            'yesterday'  => [$yesterday, $yesterday, 'Yesterday (' . Carbon::yesterday()->format('d M Y') . ')'],
            'this_month' => [
                Carbon::today()->startOfMonth()->format('Y-m-d'),
                Carbon::today()->format('Y-m-d'),
                Carbon::today()->format('F Y')
            ],
            'last_month' => [
                Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d'),
                Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d'),
                Carbon::today()->subMonth()->format('F Y')
            ],
            default => [$today, $today, 'Today (' . Carbon::today()->format('d M Y') . ')'],
        };
    }

}