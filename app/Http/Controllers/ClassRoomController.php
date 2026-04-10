<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Session;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        $class = ClassRoom::with('session')->get();
        $trashed = ClassRoom::onlyTrashed()->with('session')->get();
        return view('admin.pages.classroom.index', compact('class', 'trashed'));
    }

    public function create()
    {
        $session = Session::where('status', 1)->get();
        return view('admin.pages.classroom.create', compact('session'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required',
            'session_id' => 'required',
            'status'     => 'required',
        ]);
        ClassRoom::create([
            'class_name'   => $request->class_name,
            'section_name' => $request->section_name,
            'session_id'   => $request->session_id,
            'status'       => $request->status,
        ]);
        return redirect()->route('class.index')->with('success', 'Class created successfully');
    }

    public function show(ClassRoom $ClassRoom) {}

    public function edit($id)
    {
        $class   = ClassRoom::findOrFail($id);
        $session = Session::where('status', 1)->get();
        return view('admin.pages.classroom.edit', compact('class', 'session'));
    }

    public function update(Request $request, $id)
    {
        $classRoom = ClassRoom::findOrFail($id);
        $classRoom->update([
            'class_name'   => $request->class_name,
            'section_name' => $request->section_name,
            'session_id'   => $request->session_id,
            'status'       => $request->status,
        ]);
        return redirect()->route('class.index')->with('success', 'Class updated successfully');
    }

    public function destroy($id)
    {
        ClassRoom::findOrFail($id)->delete();
        return redirect()->route('class.index')->with('success', 'Class moved to inactive');
    }

    public function restore($id)
    {
        $classroom = ClassRoom::onlyTrashed()->findOrFail($id);
        $classroom->restore();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Class restored successfully']);
        }
        return redirect()->route('class.index')->with('success', 'Class restored successfully');
    }

    public function forceDelete($id)
    {
        ClassRoom::onlyTrashed()->findOrFail($id)->forceDelete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Class permanently deleted']);
        }
        return redirect()->route('class.index')->with('success', 'Class permanently deleted');
    }
}
