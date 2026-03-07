<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Student;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index()
    {
        $alumni = Alumni::with('student')->orderByDesc('passing_year')->get();
        return view('admin.pages.alumni.index', compact('alumni'));
    }

    public function create()
    {
        $students = Student::orderBy('student_name')->get();
        return view('admin.pages.alumni.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
        ]);

        Alumni::create($request->all());

        return redirect()->route('alumni.index')->with('success', 'Alumni record added successfully.');
    }

    public function edit(Alumni $alumnus)
    {
        $students = Student::orderBy('student_name')->get();
        return view('admin.pages.alumni.edit', compact('alumnus', 'students'));
    }

    public function update(Request $request, Alumni $alumnus)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
        ]);

        $alumnus->update($request->all());

        return redirect()->route('alumni.index')->with('success', 'Alumni record updated.');
    }

    public function destroy(Alumni $alumnus)
    {
        $alumnus->delete();
        return redirect()->route('alumni.index')->with('success', 'Alumni record deleted.');
    }
}
