<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Session;


use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $class = ClassRoom::with('session')->get();
        return view('admin.pages.classroom.index', compact('class'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $session = Session::where('status', 1)->get();
        return view('admin.pages.classroom.create', compact('session'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // Validate the incoming request data
      

        // Create a new class room record
        $classRoom = new ClassRoom();
        $classRoom->class_name = $request->input('class_name');
        $classRoom->section_name = $request->input('section_name');
        $classRoom->session_id = $request->input('session_id');
        $classRoom->status = $request->input('status');

        $classRoom->save();

        // Redirect to a success page or return a response
        return redirect()->route('class.index')->with('success', 'Class room created successfully');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassRoom $ClassRoom)
    {
        //
        

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $class = ClassRoom::find($id);
        $session = Session::where('status', 1)->get();

        return view('admin.pages.classroom.edit', compact('class','session'));



    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        //
        $classRoom =  ClassRoom::find($id);
        $classRoom->class_name = $request->input('class_name');
        $classRoom->section_name = $request->input('section_name');
        $classRoom->session_id = $request->input('session_id');
        $classRoom->status = $request->input('status');

        $classRoom->save();

        // Redirect to a success page or return a response
        return redirect()->route('class.index')->with('success', 'Class room updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
        $classroom = ClassRoom::find($id);
        $classroom->delete();
        return redirect()->route('class.index')->with('success','class deleted successfully');
    }
}
