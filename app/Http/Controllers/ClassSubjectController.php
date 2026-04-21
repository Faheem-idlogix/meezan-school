<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classSubjects = ClassSubject::with(['classRoom', 'subject'])
            ->orderBy('class_id')
            ->get();

        $groupedClassSubjects = $classSubjects
            ->filter(fn ($item) => $item->classRoom && $item->subject)
            ->groupBy('class_id');

        return view('admin.pages.class_subject.index', compact('groupedClassSubjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classRooms = $this->loadClassRoomsForDropdown();
        $usingArchivedClasses = $classRooms->contains(fn ($c) => $c->deleted_at !== null);

        $subjects = Subject::orderBy('subject_name')->get();

        return view('admin.pages.class_subject.create', compact('classRooms', 'subjects', 'usingArchivedClasses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|array|min:1',
            'subject_id.*' => 'required|exists:subjects,id',
        ]);

        foreach ($request->subject_id as $subjectId) {
            ClassSubject::firstOrCreate([
                'class_id'   => $request->class_id,
                'subject_id' => $subjectId,
            ]);
        }

        return redirect()
            ->route('class_subject.index')
            ->with('success', 'Class Subject assigned successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSubject $classSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSubject $classSubject)
    {
        $classRooms = $this->loadClassRoomsForDropdown();
        $subjects = Subject::orderBy('subject_name')->get();

        return view('admin.pages.class_subject.edit', compact('classSubject', 'classRooms', 'subjects'));
    }

    /**
     * Load classes for dropdown with a DB fallback to avoid model-scope issues.
     */
    private function loadClassRoomsForDropdown()
    {
        $classRooms = ClassRoom::withTrashed()
            ->orderBy('class_name')
            ->orderBy('section_name')
            ->get();

        if ($classRooms->isEmpty()) {
            $classRooms = DB::table('class_rooms')
                ->select('id', 'class_name', 'section_name', 'deleted_at')
                ->orderBy('class_name')
                ->orderBy('section_name')
                ->get();
        }

        return $classRooms;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassSubject $classSubject)
    {
        //
        $classSubject->class_id = $request->class_id;
        $classSubject->subject_id = $request->subject_id;
        $classSubject->save();
        return redirect()->route('class_subject.index')->with('success', 'Class Subject updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSubject $classSubject)
    {
        //
        $classSubject->delete();
        return redirect()->route('class_subject.index')->with('success', 'Class Subject unassigned successfully.');
    }
}
