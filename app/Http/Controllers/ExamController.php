<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $exams = Exam::all();
        return view('admin.pages.exam.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        
        return view('admin.pages.exam.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);
        Exam::create([
            'name' => $request->name,
            'date' => $request->date,
        ]);
        return redirect()->route('exam.index')->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        //
        return view('admin.pages.exam.edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);
        $exam->update([
            'name' => $request->name,
            'date' => $request->date,
        ]);
        return redirect()->route('exam.index')->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        //
        $exam->delete();
        return redirect()->route('exam.index')->with('success', 'Exam deleted successfully.');
    }
}
