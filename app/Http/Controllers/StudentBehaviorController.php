<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentBehavior;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class StudentBehaviorController extends Controller
{
    /**
     * List all behavior records (filterable).
     */
    public function index(Request $request)
    {
        $query = StudentBehavior::with('student', 'classRoom', 'reportedByUser')
            ->orderByDesc('incident_date');

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->class_room_id) {
            $query->where('class_room_id', $request->class_room_id);
        }

        $behaviors = $query->get();
        $classes = ClassRoom::all();
        $students = Student::orderBy('student_name')->get();
        $categories = StudentBehavior::categories();

        return view('admin.pages.behavior.index', compact('behaviors', 'classes', 'students', 'categories'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $students = Student::with('classroom')->orderBy('student_name')->get();
        $classes = ClassRoom::all();
        $categories = StudentBehavior::categories();
        return view('admin.pages.behavior.create', compact('students', 'classes', 'categories'));
    }

    /**
     * Store a behavior record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'type'          => 'required|in:positive,negative,neutral',
            'category'      => 'required|string',
            'title'         => 'required|string|max:255',
            'incident_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['reported_by'] = auth()->id();

        // Auto-set classroom from student
        if (!$request->class_room_id) {
            $student = Student::find($request->student_id);
            $data['class_room_id'] = $student->class_room_id ?? null;
        }

        StudentBehavior::create($data);

        return redirect()->route('behavior.index')->with('success', 'Behavior record added successfully.');
    }

    /**
     * Edit behavior record.
     */
    public function edit(StudentBehavior $behavior)
    {
        $students = Student::with('classroom')->orderBy('student_name')->get();
        $classes = ClassRoom::all();
        $categories = StudentBehavior::categories();
        return view('admin.pages.behavior.edit', compact('behavior', 'students', 'classes', 'categories'));
    }

    /**
     * Update behavior record.
     */
    public function update(Request $request, StudentBehavior $behavior)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'type'          => 'required|in:positive,negative,neutral',
            'category'      => 'required|string',
            'title'         => 'required|string|max:255',
            'incident_date' => 'required|date',
        ]);

        $behavior->update($request->all());

        return redirect()->route('behavior.index')->with('success', 'Behavior record updated.');
    }

    /**
     * Delete behavior record.
     */
    public function destroy(StudentBehavior $behavior)
    {
        $behavior->delete();
        return redirect()->route('behavior.index')->with('success', 'Behavior record deleted.');
    }
}
