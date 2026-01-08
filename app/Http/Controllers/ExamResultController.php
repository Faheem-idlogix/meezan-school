<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ClassSubject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;

class ExamResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exam_results = ExamResult::with(['exam', 'student', 'subject', 'classRoom'])->get();
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

            // Get students enrolled in this class
            $students = Student::where('class_room_id', $classId)
                ->select('id', 'student_name')
                ->get();

            // Get subjects assigned to this class
            $subjects = ClassSubject::where('class_id', $classId)
                ->with('subject:id,subject_name')
                ->get()
                ->map(function ($classSubject) {
                    return [
                        'id' => $classSubject->subject->id,
                        'name' => $classSubject->subject->subject_name,
                    ];
                });

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
            'student_id'  => 'required|exists:students,id',
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
                    ExamResult::create([
                        'exam_id' => $request->exam_id,
                        'student_id' => $request->student_id,
                        'subject_id' => $mark['subject_id'],
                        'class_id' => $request->class_id,
                        'total_marks' => $mark['total_marks'],
                        'obtained_marks' => $mark['obtained_marks'],
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
     * Display the specified resource.
     */
    public function show(ExamResult $examResult)
    {
        // Gather the student + exam context for a full report card
        $student   = $examResult->student()->with('classroom')->first();
        $exam      = $examResult->exam;
        $classRoom = $examResult->classRoom;

        // Pull all results for this student in this exam
        $results = ExamResult::with('subject')
            ->where('student_id', $examResult->student_id)
            ->where('exam_id', $examResult->exam_id)
            ->get();

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
            'rollNo'
        ));
        
        return view('admin.pages.exam_result.show', compact('examResult'));
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
            'student_id'     => 'required|exists:students,id',
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

        $examResult->update([
            'exam_id'        => $request->exam_id,
            'student_id'     => $request->student_id,
            'subject_id'     => $request->subject_id,
            'class_id'       => $request->class_id,
            'total_marks'    => $request->total_marks,
            'obtained_marks' => $request->obtained_marks,
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
