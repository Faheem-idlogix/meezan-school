<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Session;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    private int $schoolId = 1;

    private array $days = ['monday','tuesday','wednesday','thursday','friday','saturday'];

    public function index(Request $request)
    {
        $classId   = $request->class_id;
        $sessionId = $request->session_id;
        $classes   = ClassRoom::all();
        $sessions  = Session::all();
        $timetable = [];

        if ($classId) {
            $entries = Timetable::with('subject', 'teacher')
                                ->where('school_id', $this->schoolId)
                                ->where('class_room_id', $classId)
                                ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
                                ->where('is_active', true)
                                ->orderBy('start_time')
                                ->get();

            // Organise by day
            foreach ($this->days as $day) {
                $timetable[$day] = $entries->where('day', $day)->values();
            }
        }

        $days = $this->days;
        return view('admin.pages.timetable.index', compact('classes', 'sessions', 'classId', 'sessionId', 'timetable', 'days'));
    }

    public function create()
    {
        $classes  = ClassRoom::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $sessions = Session::all();
        return view('admin.pages.timetable.create', compact('classes', 'subjects', 'teachers', 'sessions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'    => 'required|exists:teachers,id',
            'session_id'    => 'nullable|exists:sessions,id',
            'day'           => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'room_no'       => 'nullable|string|max:20',
        ]);

        $data['school_id'] = $this->schoolId;
        Timetable::create($data);

        return redirect()->route('timetable.index', ['class_id' => $data['class_room_id']])
                         ->with('success', 'Period added to timetable.');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return back()->with('success', 'Period removed.');
    }
}
