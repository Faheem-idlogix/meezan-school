<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\ClassSubject;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $classSubjects = ClassSubject::all();
        return view('admin.pages.class_subject.index', compact('classSubjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $class =  ClassRoom::all();
        $subject = Subject::all();
        return view('admin.pages.class_subject.create', compact('class', 'subject'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        //
        
        return view('admin.pages.class_subject.edit', compact('classSubject'));
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
