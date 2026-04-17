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

        // Group by exam + class for display
        $grouped = $schedules->groupBy(function ($item) {
            return $item->exam_id . '-' . $item->class_room_id;
        });

        $exams = Exam::orderByDesc('date')->get();
        $classRooms = ClassRoom::all();

        return view('admin.pages.exam_schedule.index', compact('grouped', 'exams', 'classRooms'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $exams = Exam::orderByDesc('date')->get();
        $classRooms = ClassRoom::all();
        $subjects = Subject::all();
        return view('admin.pages.exam_schedule.create', compact('exams', 'classRooms', 'subjects'));
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

    /**
     * Edit all schedule entries for an exam + class group.
     */
    public function editGroup(Request $request)
    {
        $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'class_room_id' => 'required|exists:class_rooms,id',
        ]);

        $exam      = Exam::findOrFail($request->exam_id);
        $classRoom = ClassRoom::findOrFail($request->class_room_id);

        $schedules = ExamSchedule::with('subject')
            ->where('exam_id', $request->exam_id)
            ->where('class_room_id', $request->class_room_id)
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        $exams      = Exam::orderByDesc('date')->get();
        $classRooms = ClassRoom::all();
        $subjects   = Subject::all();

        return view('admin.pages.exam_schedule.edit', compact('schedules', 'exam', 'classRoom', 'exams', 'classRooms', 'subjects'));
    }

    /**
     * Update all schedule entries for an exam + class group.
     */
    public function updateGroup(Request $request)
    {
        $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'entries'       => 'required|array|min:1',
            'entries.*.subject_id'  => 'required|exists:subjects,id',
            'entries.*.exam_date'   => 'required|date',
        ]);

        $examId     = $request->exam_id;
        $classRoomId = $request->class_room_id;

        // Collect IDs that are being kept
        $keepIds = collect($request->entries)->pluck('id')->filter()->all();

        // Delete entries removed by user
        ExamSchedule::where('exam_id', $examId)
            ->where('class_room_id', $classRoomId)
            ->whereNotIn('id', $keepIds)
            ->delete();

        // Update existing + create new
        foreach ($request->entries as $entry) {
            $data = [
                'exam_id'       => $examId,
                'class_room_id' => $classRoomId,
                'subject_id'    => $entry['subject_id'],
                'exam_date'     => $entry['exam_date'],
                'start_time'    => $entry['start_time'] ?? null,
                'end_time'      => $entry['end_time'] ?? null,
                'room'          => $entry['room'] ?? null,
                'total_marks'   => $entry['total_marks'] ?? null,
                'passing_marks' => $entry['passing_marks'] ?? null,
            ];

            if (!empty($entry['id'])) {
                ExamSchedule::where('id', $entry['id'])->update($data);
            } else {
                ExamSchedule::create($data);
            }
        }

        return redirect()->route('exam-schedules.index', ['exam_id' => $examId])
            ->with('success', 'Exam schedule updated successfully.');
    }

    /**
     * Print exam schedule (date-sheet) for a given exam + class.
     */
    public function print(Request $request)
    {
        $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'class_room_id' => 'required|exists:class_rooms,id',
        ]);

        $exam      = Exam::findOrFail($request->exam_id);
        $classRoom = ClassRoom::findOrFail($request->class_room_id);

        $schedules = ExamSchedule::with('subject')
            ->where('exam_id', $request->exam_id)
            ->where('class_room_id', $request->class_room_id)
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        return view('admin.pages.exam_schedule.print', compact('schedules', 'exam', 'classRoom'));
    }
}
