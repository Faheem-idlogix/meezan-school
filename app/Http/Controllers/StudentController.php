<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource (active only).
     */
    public function index()
    {
        $student = Student::with('classroom')->get();
        $trashed = Student::onlyTrashed()->with('classroom')->get();
        return view('admin.pages.student.index', compact('student', 'trashed'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $class_room = ClassRoom::all();
        return view('admin.pages.student.create', compact('class_room'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'class_room_id' => 'required',
            'student_status' => 'required',
        ]);
        $input = $request->all();
        $input['student_name'] = ($request->first_name . ' ' . $request->last_name);
        $slug = Str::slug($input['student_name']);
        $check = Student::withTrashed()->where('slug', $slug)->first();
        if ($check != null) {
            $input['slug'] = Str::slug($input['student_name'] . '-' . rand(1, 1000));
            $input['student_email'] = ($input['slug'] . '@meezan.edu.pk');
        } else {
            $input['slug'] = Str::slug($input['student_name']);
            $input['student_email'] = ($input['slug'] . '@meezan.edu.pk');
        }
        if ($image = $request->file('student_image')) {
            $destinationPath = public_path('img/students/');
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['student_image'] = $profileImage;
        }
        Student::create($input);

        return redirect()->route('student.create')->with('success', 'Student added successfully');
    }

    /**
     * Display the specified resource (comprehensive profile).
     */
    public function show(Student $student)
    {
        $student->load([
            'classroom',
            'attendance' => fn($q) => $q->orderByDesc('date'),
            'fees' => fn($q) => $q->orderByDesc('created_at'),
            'examResults.exam',
            'examResults.subject',
            'examResults.classRoom',
        ]);

        // ── Attendance summary ──
        $totalAttendance = $student->attendance->count();
        $presentCount    = $student->attendance->whereIn('attendance', ['1', 1])->count();
        $absentCount     = $student->attendance->whereIn('attendance', ['3', 3])->count();
        $leaveCount      = $student->attendance->whereIn('attendance', ['2', 2])->count();
        $lateCount       = $student->attendance->whereIn('attendance', ['0', 0])->count();
        $attendanceRate  = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;

        // ── Monthly attendance (last 6 months) for mini chart ──
        $monthlyAtt = collect();
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $mKey = $m->format('Y-m');
            $monthRecs = $student->attendance->filter(function ($a) use ($mKey) {
                // date may be "Y-m-d" or "d-m-Y"
                $d = strlen($a->date) === 10 && substr($a->date, 4, 1) === '-' ? $a->date : null;
                if (!$d) {
                    try { $d = \Carbon\Carbon::parse($a->date)->format('Y-m-d'); } catch (\Exception $e) { $d = ''; }
                }
                return str_starts_with($d, $mKey);
            });
            $monthlyAtt->push([
                'label'   => $m->format('M'),
                'present' => $monthRecs->whereIn('attendance', ['1', 1])->count(),
                'absent'  => $monthRecs->whereIn('attendance', ['3', 3])->count(),
                'total'   => $monthRecs->count(),
            ]);
        }

        // ── Fee summary ──
        $totalFees   = $student->fees->sum('total_fee');
        $paidFees    = $student->fees->sum('received_payment_fee');
        $pendingFees = $totalFees - $paidFees;

        // ── Exam results grouped by exam ──
        $examGroups = $student->examResults->groupBy(fn($r) => $r->exam->name ?? 'Unknown Exam');

        return view('admin.pages.student.show', compact(
            'student',
            'totalAttendance', 'presentCount', 'absentCount', 'leaveCount', 'lateCount', 'attendanceRate',
            'monthlyAtt',
            'totalFees', 'paidFees', 'pendingFees',
            'examGroups'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $Student)
    {
        $class_room = ClassRoom::all();
        return view('admin.pages.student.edit', compact('Student', 'class_room'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $Student)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'class_room_id' => 'required',
            'student_status' => 'required',
        ]);
        $input = $request->all();
        $input['student_name'] = ($request->first_name . ' ' . $request->last_name);
        if ($image = $request->file('student_image')) {
            $destinationPath = public_path('img/students/');
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['student_image'] = $profileImage;
        }
        $Student->update($input);

        return redirect()->route('student.index')->with('success', 'Student updated successfully');
    }

    /**
     * Soft-delete the specified resource.
     */
    public function destroy(Student $Student)
    {
        $Student->delete();
        return redirect()->route('student.index')->with('success', 'Student moved to inactive');
    }

    /**
     * Restore a soft-deleted student (AJAX).
     */
    public function restore($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Student restored successfully']);
        }
        return redirect()->route('student.index')->with('success', 'Student restored successfully');
    }

    /**
     * Permanently delete a student (AJAX).
     */
    public function forceDelete($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->forceDelete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Student permanently deleted']);
        }
        return redirect()->route('student.index')->with('success', 'Student permanently deleted');
    }

    /**
     * AJAX: Get students by class (active only).
     */
    public function getStudentsByClass(Request $request)
    {
        $students = Student::where('class_room_id', $request->class_id)->get(['id', 'student_name']);
        return response()->json(['students' => $students]);
    }
}
