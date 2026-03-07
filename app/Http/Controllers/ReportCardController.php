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
        $classes = ClassRoom::all();
        $gradingSystems = GradingSystem::active()->get();
        return view('admin.pages.report_card.config', compact('configs', 'classes', 'gradingSystems'));
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
        return view('admin.pages.report_card.generate', compact('exams', 'classes'));
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

        $student = Student::with('classroom')->findOrFail($request->student_id);
        $exam = Exam::with('gradingSystem.gradeRules')->findOrFail($request->exam_id);

        $results = ExamResult::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->with('subject')
            ->get();

        // Calculate grades if grading system exists
        $gradingSystem = $exam->gradingSystem ?? GradingSystem::where('is_default', true)->first();

        $totalMarks = 0;
        $obtainedMarks = 0;

        foreach ($results as $result) {
            $totalMarks += $result->total_marks;
            $obtainedMarks += $result->obtained_marks;

            if ($gradingSystem && $result->total_marks > 0) {
                $percentage = ($result->obtained_marks / $result->total_marks) * 100;
                $gradeRule = $gradingSystem->getGradeForPercentage($percentage);

                $result->calculated_grade = $gradeRule ? $gradeRule->grade : 'N/A';
                $result->calculated_gp = $gradeRule ? $gradeRule->grade_point : 0;
                $result->calculated_percentage = round($percentage, 2);
            }
        }

        $overallPercentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
        $overallGrade = null;
        $gpa = 0;

        if ($gradingSystem) {
            $overallGrade = $gradingSystem->getGradeForPercentage($overallPercentage);
            $gpa = $results->count() > 0 ? round($results->avg('calculated_gp'), 2) : 0;
        }

        // Get config
        $config = ReportCardConfig::where('class_room_id', $student->class_room_id)->first()
            ?? ReportCardConfig::where('is_default', true)->first()
            ?? new ReportCardConfig();

        // Attendance summary
        $attendanceCount = $student->attendance()
            ->whereYear('created_at', now()->year)
            ->count();
        $presentCount = $student->attendance()
            ->whereYear('created_at', now()->year)
            ->whereIn('attendance', ['1', 1])
            ->count();

        $pdf = Pdf::loadView('admin.pages.report_card.pdf', compact(
            'student', 'exam', 'results', 'gradingSystem',
            'totalMarks', 'obtainedMarks', 'overallPercentage',
            'overallGrade', 'gpa', 'config',
            'attendanceCount', 'presentCount'
        ));

        return $pdf->stream('Report-Card-' . $student->slug . '-' . $exam->name . '.pdf');
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
