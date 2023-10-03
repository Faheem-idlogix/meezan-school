<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $session = Session::all();
        return view('admin.pages.session.index', compact('session'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.pages.session.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->validate([
            'session_name' => 'required',
            'status' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',            
            ]);
            $data = Session::create($input);
            return redirect()->route('session.create')->with('success','Session add successfully');


        }

    /**
     * Display the specified resource.
     */
    public function show(Session $session)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Session $Session)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Session $session)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $Session)
    {
        $Session->delete();
        return redirect()->route('session.index')->with('success','Session deleted successfully');
    }
}
