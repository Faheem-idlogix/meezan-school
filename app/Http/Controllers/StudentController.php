<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $student = Student::with('classroom')->get();
        return view('admin.pages.student.index', compact('student'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $class_room = ClassRoom::all();
        return view('admin.pages.student.create', compact('class_room'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'class_room_id' => 'required',
            'student_status' => 'required',
            
            ]);
        $input = $request->all();
        $input['student_name'] = ($request->first_name . ' ' . $request->last_name);
        $slug= Str::slug( $input['student_name']);
        $check = Student::withTrashed()->where('slug', $slug)->first();
        if ($check != null) {
            $input['slug']= Str::slug( $input['student_name'].'-'.rand(1,1000));
            $input['student_email'] = ($input['slug'] . '@meezan.edu.pk');

        }
        else{
            $input['slug']= Str::slug( $input['student_name']);
            $input['student_email'] = ($input['slug'] . '@meezan.edu.pk');
        }
        if ($image = $request->file('student_image')) {
            $destinationPath = public_path('img/students/');
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['student_image'] = $profileImage;
        };
        $data = Student::create($input);

        // $user['name'] = ($request->first_name . ' ' . $request->last_name);
        // $user['email'] = ($input['slug'] . '@meezan.edu.pk');
        // $user['password'] = bcrypt($request->first_name . $request->last_name.'123');
        // $user['user_type_id'] = '4';
        // $data1 = User::create($user);

        return redirect()->route('student.create')->with('success','Student add successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $Student)
    {
        //
        $class_room = ClassRoom::all();
        return view('admin.pages.student.edit', compact('Student','class_room'));


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $Student)
    {
        //
        $input = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'class_room_id' => 'required',
            'student_status' => 'required',
            
            ]);
        $input = $request->all();
        $input['student_name'] = ($request->first_name . ' ' . $request->last_name);
        $data = $Student->update($input);


        return redirect()->route('student.create')->with('success','Student add successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $Student)
    {
          $Student->delete();
          return redirect()->route('student.index')->with('success','Conference Category deleted successfully');
    }
}
