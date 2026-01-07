<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $subject = Subject::all();
        return view('admin.pages.subject.index', compact('subject'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.pages.subject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->validate([
            'subject_code' => 'required',
            'subject_name' => 'required',           
            ]);
            $data = Subject::create($input);
            return redirect()->route('subject.create')->with('success','Subject add successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $Subject)
    {
        //
        return view('admin.pages.Subject.edit', compact('Subject'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $Subject)
    {
        //
        $input = $request->validate([
            'subject_code' => 'required',
            'subject_name' => 'required',           
            ]);
            $data = $Subject->update($input);
            return redirect()->route('subject.index')->with('success','Subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $Subject)
    {
        $Subject->delete();
        return redirect()->route('subject.index')->with('success','Subject deleted successfully');
    }
}
