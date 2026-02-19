<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ClassRoom;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::all();
        $trashed  = Teacher::onlyTrashed()->get();
        return view('admin.pages.teacher.index', compact('teachers', 'trashed'));
    }

    public function create()
    {
        $classrooms = ClassRoom::all();
        return view('admin.pages.teacher.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required',
            'last_name'      => 'required',
            'gender'         => 'required',
            'teacher_status' => 'required',
        ]);

        $input = $request->all();
        $input['teacher_name'] = $request->first_name . ' ' . $request->last_name;
        $slug  = Str::slug($input['teacher_name']);
        $check = Teacher::withTrashed()->where('slug', $slug)->first();
        $input['slug']          = $check ? Str::slug($input['teacher_name'] . '-' . rand(1, 9999)) : $slug;
        $input['teacher_email'] = $input['slug'] . '@meezan.edu.pk';

        if ($image = $request->file('teacher_image')) {
            $destinationPath = public_path('img/teachers/');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);
            $profileImage = date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['teacher_image'] = $profileImage;
        }

        Teacher::create($input);
        return redirect()->route('teacher.index')->with('success', 'Teacher added successfully');
    }

    public function show(Teacher $teacher)
    {
        return view('admin.pages.teacher.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $classrooms = ClassRoom::all();
        return view('admin.pages.teacher.edit', compact('teacher', 'classrooms'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'first_name'     => 'required',
            'last_name'      => 'required',
            'gender'         => 'required',
            'teacher_status' => 'required',
        ]);

        $input = $request->all();
        $input['teacher_name'] = $request->first_name . ' ' . $request->last_name;

        if ($image = $request->file('teacher_image')) {
            $destinationPath = public_path('img/teachers/');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);
            $profileImage = date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['teacher_image'] = $profileImage;
        }

        $teacher->update($input);
        return redirect()->route('teacher.index')->with('success', 'Teacher updated successfully');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('teacher.index')->with('success', 'Teacher moved to inactive');
    }

    public function restore($id)
    {
        $teacher = Teacher::onlyTrashed()->findOrFail($id);
        $teacher->restore();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Teacher restored successfully']);
        }
        return redirect()->route('teacher.index')->with('success', 'Teacher restored successfully');
    }

    public function forceDelete($id)
    {
        Teacher::onlyTrashed()->findOrFail($id)->forceDelete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Teacher permanently deleted']);
        }
        return redirect()->route('teacher.index')->with('success', 'Teacher permanently deleted');
    }
}
