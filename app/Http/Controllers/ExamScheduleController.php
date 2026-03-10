<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\Exam;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{
    /**
     * Show date-sheet for an exam (or all exams).
     */
    public function index(Request $request)
    {
        $query = ExamSchedule::with('exam', 'classRoom', 'subject')
            ->orderBy('exam_date')
            ->orderBy('start_time');

        if ($request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->class_room_id) {
            $query->where('class_room_id', $request->class_room_id);
        }

        $schedules = $query->get();
        $exams = Exam::orderByDesc('date')->get();
        $classRooms = ClassRoom::all();

        return view('admin.pages.exam_schedule.index', compact('schedules', 'exams', 'classRooms'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $exams = Exam::orderByDesc('date')->get();
        $classes = ClassRoom::all();
        $subjects = Subject::all();
        return view('admin.pages.exam_schedule.create', compact('exams', 'classes', 'subjects'));
    }

    /**
     * Store schedule entries.
     */
    public function store(Request $request)
    {
        $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'entries'        => 'required|array|min:1',
            'entries.*.subject_id'  => 'required|exists:subjects,id',
            'entries.*.exam_date'   => 'required|date',
        ]);

        foreach ($request->entries as $entry) {
            ExamSchedule::create([
                'exam_id'       => $request->exam_id,
                'class_room_id' => $request->class_room_id,
                'subject_id'    => $entry['subject_id'],
                'exam_date'     => $entry['exam_date'],
                'start_time'    => $entry['start_time'] ?? null,
                'end_time'      => $entry['end_time'] ?? null,
                'room'          => $entry['room'] ?? null,
                'total_marks'   => $entry['total_marks'] ?? null,
                'passing_marks' => $entry['passing_marks'] ?? null,
            ]);
        }

        return redirect()->route('exam-schedules.index', ['exam_id' => $request->exam_id])
            ->with('success', 'Exam schedule created with ' . count($request->entries) . ' entries.');
    }

    /**
     * Delete a schedule entry.
     */
    public function destroy(ExamSchedule $examSchedule)
    {
        $examSchedule->delete();
        return redirect()->back()->with('success', 'Schedule entry deleted.');
    }
}
