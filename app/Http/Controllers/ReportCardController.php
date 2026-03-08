<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\GradingSystem;
use App\Models\ReportCardConfig;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    /**
     * Report card configuration page.
     */
    public function config()
    {
        $configs = ReportCardConfig::with('classRoom', 'gradingSystem')->get();
        $classRooms = ClassRoom::all();
        $gradingSystems = GradingSystem::active()->get();
        return view('admin.pages.report_card.config', compact('configs', 'classRooms', 'gradingSystems'));
    }

    /**
     * Store/update a report card config.
     */
    public function storeConfig(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['show_grade']      = $request->boolean('show_grade');
        $data['show_gpa']        = $request->boolean('show_gpa');
        $data['show_position']   = $request->boolean('show_position');
        $data['show_percentage'] = $request->boolean('show_percentage');
        $data['show_remarks']    = $request->boolean('show_remarks');
        $data['show_attendance'] = $request->boolean('show_attendance');
        $data['show_behavior']   = $request->boolean('show_behavior');
        $data['is_default']      = $request->boolean('is_default');

        if ($request->config_id) {
            $config = ReportCardConfig::findOrFail($request->config_id);
            $config->update($data);
        } else {
            ReportCardConfig::create($data);
        }

        if ($request->boolean('is_default')) {
            ReportCardConfig::where('id', '!=', $request->config_id ?? 0)->update(['is_default' => false]);
        }

        return redirect()->route('report-cards.config')->with('success', 'Report card configuration saved.');
    }

    /**
     * Generate report card selection page.
     */
    public function generate()
    {
        $exams = Exam::orderByDesc('date')->get();
        $classes = ClassRoom::all();
        $students = Student::with('classroom')->whereNull('deleted_at')->orderBy('student_name')->get();
        return view('admin.pages.report_card.generate', compact('exams', 'classes', 'students'));
    }

    /**
     * Generate report card PDF for a student + exam.
     */
    public function pdf(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_id'    => 'required|exists:exams,id',
        ]);

        $student   = Student::with('classroom')->findOrFail($request->student_id);
        $exam      = Exam::findOrFail($request->exam_id);
        $classRoom = $student->classroom;

        // Pull all results for this student in this exam
        $results = ExamResult::with('subject')
            ->where('student_id', $student->id)
            ->where('exam_id', $exam->id)
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

        $pdf = Pdf::loadView('admin.pages.report_card.pdf', compact(
            'student', 'classRoom', 'exam', 'results',
            'totalMax', 'totalObt', 'percentage',
            'overallGrade', 'overallRemark', 'rollNo'
        ));

        return $pdf->stream('Report-Card-' . ($student->slug ?? $student->id) . '-' . $exam->name . '.pdf');
    }

    /**
     * Grade label helper (matches ExamResultController logic).
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

    /**
     * Calculate & update grades + positions for an exam + class.
     */
    public function calculateGrades(Request $request)
    {
        $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'class_room_id' => 'required|exists:class_rooms,id',
        ]);

        $exam = Exam::with('gradingSystem.gradeRules')->findOrFail($request->exam_id);
        $gradingSystem = $exam->gradingSystem ?? GradingSystem::where('is_default', true)->first();

        if (!$gradingSystem) {
            return redirect()->back()->with('error', 'No grading system found. Please create one first.');
        }

        $results = ExamResult::where('exam_id', $exam->id)
            ->where('class_id', $request->class_room_id)
            ->get();

        // Calculate percentage, grade, grade_point for each result
        foreach ($results as $result) {
            if ($result->total_marks > 0) {
                $pct = ($result->obtained_marks / $result->total_marks) * 100;
                $gradeRule = $gradingSystem->getGradeForPercentage($pct);

                $result->update([
                    'percentage'    => round($pct, 2),
                    'grade'         => $gradeRule ? $gradeRule->grade : null,
                    'grade_point'   => $gradeRule ? $gradeRule->grade_point : null,
                ]);
            }
        }

        // Calculate positions — group by student, sum obtained marks, rank
        $studentTotals = $results->groupBy('student_id')->map(function ($studentResults) {
            return $studentResults->sum('obtained_marks');
        })->sortDesc();

        $position = 1;
        $lastTotal = null;
        $sameRank = 0;

        foreach ($studentTotals as $studentId => $total) {
            if ($total === $lastTotal) {
                $sameRank++;
            } else {
                $position += $sameRank;
                $sameRank = 0;
            }

            ExamResult::where('exam_id', $exam->id)
                ->where('class_id', $request->class_room_id)
                ->where('student_id', $studentId)
                ->update(['class_position' => $position]);

            $lastTotal = $total;
        }

        // Subject positions
        $subjects = $results->pluck('subject_id')->unique();
        foreach ($subjects as $subjectId) {
            $subjectResults = $results->where('subject_id', $subjectId)->sortByDesc('obtained_marks');
            $pos = 1;
            $lastMarks = null;
            $sameRank = 0;

            foreach ($subjectResults as $result) {
                if ($result->obtained_marks === $lastMarks) {
                    $sameRank++;
                } else {
                    $pos += $sameRank;
                    $sameRank = 0;
                }
                $result->update(['subject_position' => $pos]);
                $lastMarks = $result->obtained_marks;
            }
        }

        return redirect()->back()->with('success', 'Grades and positions calculated for ' . $studentTotals->count() . ' students.');
    }

    /**
     * Approve all results for an exam + class.
     */
    public function approveResults(Request $request)
    {
        $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'class_room_id' => 'required|exists:class_rooms,id',
        ]);

        ExamResult::where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_room_id)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'approved_by'     => auth()->id(),
                'approved_at'     => now(),
            ]);

        return redirect()->back()->with('success', 'All pending results approved.');
    }
}
