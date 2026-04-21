<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ClassSubject;
use App\Models\ClassRoom;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Group results by student — show unique student+exam+class combos
        $exam_results = ExamResult::with(['exam', 'student', 'classRoom'])
            ->whereHas('student', function ($q) {
                $q->whereNull('deleted_at')
                    ->whereIn('student_status', ['active', '1']);
            })
            ->select('student_id', 'exam_id', 'class_id')
            ->selectRaw('COUNT(*) as subject_count')
            ->selectRaw('SUM(total_marks) as total_marks_sum')
            ->selectRaw('SUM(obtained_marks) as obtained_marks_sum')
            ->groupBy('student_id', 'exam_id', 'class_id')
            ->get();

        // Eager load relationships on the grouped results
        $exam_results->load(['student', 'exam', 'classRoom']);

        return view('admin.pages.exam_result.index', compact('exam_results'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exams = Exam::all();
        $classRooms = ClassRoom::all();
        return view('admin.pages.exam_result.create', compact('exams', 'classRooms'));
    }

    /**
     * Get students and subjects for a class via AJAX
     */
    public function getClassData($classId)
    {
        try {
            // Verify class exists
            $classRoom = ClassRoom::findOrFail($classId);

            // Get active students enrolled in this class
            $students = Student::where('class_room_id', $classId)
                ->where(function ($q) {
                    $q->where('student_status', 'active')
                      ->orWhere('student_status', '1');
                })
                ->select('id', 'student_name')
                ->orderBy('student_name')
                ->get();

            // Get subjects assigned to this class
            $subjects = ClassSubject::where('class_id', $classId)
                ->with('subject:id,subject_name')
                ->get()
                ->filter(fn($cs) => $cs->subject !== null)
                ->map(function ($classSubject) {
                    return [
                        'id' => $classSubject->subject->id,
                        'name' => $classSubject->subject->subject_name,
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'students' => $students,
                'subjects' => $subjects,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found',
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Handles bulk insert for multiple subjects
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'exam_id'     => 'required|exists:exams,id',
            'class_id'    => 'required|exists:class_rooms,id',
            'student_id'  => [
                'required',
                Rule::exists('students', 'id')->where(function ($q) {
                    $q->whereNull('deleted_at')
                        ->whereIn('student_status', ['active', '1']);
                }),
            ],
            'marks'       => 'required|array',
            'marks.*.subject_id' => 'required|integer|exists:subjects,id',
            'marks.*.total_marks' => 'required|numeric|min:0',
            'marks.*.obtained_marks' => 'required|numeric|min:0',
        ]);
         // Begin transaction

        try {
            $created = 0;
            $skipped = 0;

            // Process each subject
            foreach ($request->marks as $mark) {
                // Check for duplicate (prevent unique constraint violation)
                $exists = ExamResult::where([
                    'student_id' => $request->student_id,
                    'subject_id' => $mark['subject_id'],
                    'exam_id' => $request->exam_id,
                ])->exists();

                if (!$exists) {
                    $total = (float) $mark['total_marks'];
                    $obtained = (float) $mark['obtained_marks'];
                    $pct = $total > 0 ? round(($obtained / $total) * 100, 2) : 0;
                    [$grade, $remark] = $this->gradeLabel($pct);

                    ExamResult::create([
                        'exam_id' => $request->exam_id,
                        'student_id' => $request->student_id,
                        'subject_id' => $mark['subject_id'],
                        'class_id' => $request->class_id,
                        'total_marks' => $total,
                        'obtained_marks' => $obtained,
                        'percentage' => $pct,
                        'grade' => $grade,
                        'teacher_remarks' => $remark,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            $message = "Exam results recorded: $created created";
            if ($skipped > 0) {
                $message .= ", $skipped skipped (already exist)";
            }

            return redirect()
                ->route('exam_result.index')
                ->with('success', $message . '.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error saving exam results: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show all subjects for a student in an exam (detail page).
     */
    public function studentDetail($studentId, $examId)
    {
        $results = ExamResult::with(['subject', 'student.classroom', 'exam'])
            ->where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->get();

        if ($results->isEmpty()) {
            return redirect()->route('exam_result.index')->with('error', 'No results found.');
        }

        $student   = $results->first()->student;
        $exam      = $results->first()->exam;
        $classRoom = $student->classroom;

        // Add computed fields
        $results = $results->map(function ($res) {
            $total = (float) ($res->total_marks ?? 0);
            $obt   = (float) ($res->obtained_marks ?? 0);
            $pct   = $total > 0 ? round(($obt / $total) * 100, 2) : 0;
            [$grade, $remark] = $this->gradeLabel($pct);
            $res->percentage = $pct;
            $res->grade      = $grade;
            $res->remark     = $remark;
            return $res;
        });

        $totalMax   = $results->sum('total_marks');
        $totalObt   = $results->sum('obtained_marks');
        $percentage = $totalMax > 0 ? round(($totalObt / $totalMax) * 100, 2) : 0;
        [$overallGrade, $overallRemark] = $this->gradeLabel($percentage);

        return view('admin.pages.exam_result.student_detail', compact(
            'results', 'student', 'exam', 'classRoom',
            'totalMax', 'totalObt', 'percentage', 'overallGrade', 'overallRemark'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $examResult = ExamResult::find($id);

        if (!$examResult) {
            return redirect()->route('exam_result.index')
                ->with('error', 'Selected exam result was not found.');
        }

        // Gather the student + exam context for a full report card
        $student = Student::withTrashed()
            ->with('classroom')
            ->find($examResult->student_id);
        $exam = Exam::withTrashed()->find($examResult->exam_id);
        $classRoom = ClassRoom::withTrashed()->find($examResult->class_id);

        if (!$student || !$exam || !$classRoom) {
            return redirect()->route('exam_result.index')
                ->with('error', 'Result record is incomplete (student, class, or exam missing).');
        }

        // Pull all results for this student in this exam
        $results = ExamResult::with('subject')
            ->where('student_id', $examResult->student_id)
            ->where('exam_id', $examResult->exam_id)
            ->get();

        if ($results->isEmpty()) {
            return redirect()->route('exam_result.index')
                ->with('error', 'No subject-wise marks found for this student and exam.');
        }

        // Add computed fields: percentage, grade, remark
        $results = $results->map(function ($res) {
            $total = (float) ($res->total_marks ?? 0);
            $obt   = (float) ($res->obtained_marks ?? 0);
            $pct   = $total > 0 ? round(($obt / $total) * 100, 2) : 0;

            [$grade, $remark] = $this->gradeLabel($pct);

            $res->percentage = $pct;
            $res->grade      = $grade;
            $res->remark     = $remark;

            return $res;
        });

        $totalMax   = $results->sum('total_marks');
        $totalObt   = $results->sum('obtained_marks');
        $percentage = $totalMax > 0 ? round(($totalObt / $totalMax) * 100, 2) : 0;
        [$overallGrade, $overallRemark] = $this->gradeLabel($percentage);

        $rollNo = 'MS-' . str_pad((string) $student->id, 4, '0', STR_PAD_LEFT);

        // Get attendance data for this student
        $attendanceData = Attendance::where('student_id', $student->id)
            ->where('class_room_id', $examResult->class_id)
            ->selectRaw("COUNT(*) as total_days")
            ->selectRaw("SUM(CASE WHEN attendance = '1' THEN 1 ELSE 0 END) as present_days")
            ->selectRaw("SUM(CASE WHEN attendance = '3' THEN 1 ELSE 0 END) as absent_days")
            ->selectRaw("SUM(CASE WHEN attendance = '2' THEN 1 ELSE 0 END) as leave_days")
            ->first();

        $totalDays = (int) ($attendanceData->total_days ?? 0);
        $presentDays = (int) ($attendanceData->present_days ?? 0);
        $absentDays = (int) ($attendanceData->absent_days ?? 0);

        return view('admin.pages.exam_result.show', compact(
            'student',
            'classRoom',
            'exam',
            'results',
            'totalMax',
            'totalObt',
            'percentage',
            'overallGrade',
            'overallRemark',
            'rollNo',
            'totalDays',
            'presentDays',
            'absentDays'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamResult $examResult)
    {
        $exams = Exam::all();
        $classRooms = ClassRoom::all();
        
        return view('admin.pages.exam_result.edit', compact('examResult', 'exams', 'classRooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamResult $examResult)
    {
        $request->validate([
            'exam_id'        => 'required|exists:exams,id',
            'class_id'       => 'required|exists:class_rooms,id',
            'student_id'     => [
                'required',
                Rule::exists('students', 'id')->where(function ($q) {
                    $q->whereNull('deleted_at')
                        ->whereIn('student_status', ['active', '1']);
                }),
            ],
            'subject_id'     => 'required|exists:subjects,id',
            'total_marks'    => 'required|numeric|min:0',
            'obtained_marks' => 'required|numeric|min:0',
        ]);

        // Check if this combination already exists for a DIFFERENT record
        $duplicate = ExamResult::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->where('exam_id', $request->exam_id)
            ->where('id', '!=', $examResult->id)
            ->exists();

        if ($duplicate) {
            return redirect()
                ->back()
                ->with('error', 'This student already has a result for this subject in this exam.')
                ->withInput();
        }

        $total = (float) $request->total_marks;
        $obtained = (float) $request->obtained_marks;
        $pct = $total > 0 ? round(($obtained / $total) * 100, 2) : 0;
        [$grade, $remark] = $this->gradeLabel($pct);

        $examResult->update([
            'exam_id'        => $request->exam_id,
            'student_id'     => $request->student_id,
            'subject_id'     => $request->subject_id,
            'class_id'       => $request->class_id,
            'total_marks'    => $total,
            'obtained_marks' => $obtained,
            'percentage'     => $pct,
            'grade'          => $grade,
            'teacher_remarks' => $remark,
        ]);

        return redirect()
            ->route('exam_result.index')
            ->with('success', 'Exam result updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamResult $examResult)
    {
        //
        $examResult->delete();
        return redirect()->route('exam_result.index')->with('success', 'Exam result deleted successfully.');
    }

    /**
     * Resolve grade and remark based on percentage
     */
    private function gradeLabel(float $percentage): array
    {
        $scale = [
            ['min' => 90, 'grade' => 'A+', 'remark' => 'Excellent'],
            ['min' => 80, 'grade' => 'A',  'remark' => 'Very Good'],
            ['min' => 70, 'grade' => 'B',  'remark' => 'Good'],
            ['min' => 60, 'grade' => 'C',  'remark' => 'Average'],
            ['min' => 50, 'grade' => 'D',  'remark' => 'Average'],
            ['min' => 0,  'grade' => 'F',  'remark' => 'Fail'],
        ];

        foreach ($scale as $row) {
            if ($percentage >= $row['min']) {
                return [$row['grade'], $row['remark']];
            }
        }

        return ['N/A', ''];
    }
}
