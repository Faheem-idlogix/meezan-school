<?php

namespace App\Http\Controllers;

use App\Models\StudentFee;
use App\Models\Student;
use App\Models\ClassRoom;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

use Illuminate\Http\Request;

class StudentFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        //
        $studentFee = StudentFee::where('class_fee_voucher_id', $id)->get();
        return view('admin.pages.student_fee.index', compact('studentFee'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentFee $studentFee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $studentFee = StudentFee::with('class_fee_voucher', 'student')->find($id);
        $class_room = ClassRoom::all();
        $student = Student::all();
        return view('admin.pages.student_fee.edit', compact('studentFee', 'class_room', 'student'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $total_fee = $request->stationery_charges +
        $request->test_series_charges +
        $request->exam_charges +
        $request-> notebook_charges+
        $request-> book_charges+
        $request->fine +
        $request->arrears +
        $request->academic_fee;
        $carbonDate = Carbon::parse($request->issue_date);
        $fee_month = $carbonDate->format('F Y'); 
        $student_fee =  StudentFee::find($id);
        $student_fee->voucher_no = StudentFee::generateUniqueVoucherNumber();
        $student_fee->fee_month = $fee_month;
        $student_fee->issue_date = $request->issue_date;
        $student_fee->submit_date = $request->submit_date;
        $student_fee->total_fee = $total_fee;
        $student_fee->stationery_charges = $request->stationery_charges;
        $student_fee->test_series_charges = $request->test_series_charges;
        $student_fee->exam_charges = $request->exam_charges;
        $student_fee->book_charges = $request->book_charges;
        $student_fee->notebook_charges = $request->notebook_charges;
        $student_fee->fine = $request->fine;
        $student_fee->arrears = $request->arrears;
        $student_fee->academic_fee = $request->academic_fee;
        $student_fee->note = $request->note;
        $student_fee->status = $request->status;
        $student_fee->save();
        return redirect()->back()->with('success','Student Fee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentFee $studentFee)
    {
        //
    }

    public function add_fee(Request $request){
        $studentFee = StudentFee::find($request->id);
        $total_fee = $studentFee->total_fee;
        $fee = intval($request->fee);
        if($fee > $total_fee){
            return response()->json([
                'message' => 'Enter a valid Fee Amount must be equal or less than total fee',
                'flash' => view('flash::message')->render(),
        ]);
        }
        else if($request->fee != null && $request->fee != '' && $request->fee != '0'){
            $student_fee = StudentFee::find($request->id);
            $student_fee->received_payment_fee = $request->fee;
            if($fee == $total_fee){
            $student_fee->status = 'paid';
            }
            if($fee < $total_fee){
                $student_fee->status = 'pending';
            }
             $fee_charges_left = $total_fee - $fee;
            $student_fee->fee_charges_left = $fee_charges_left;
            $student_fee->save();
            $student_name = $student_fee->student->student_name;
            $statusView = View::make('admin.pages.student_fee.status_view', ['status' => $student_fee->status, 'student' => $student_fee])->render();
                return response()->json(['student' => $student_fee, 'updatedStatusHTML' => $statusView ]);
        }
        else{
            return response()->json([
                'message' => 'Please enter the Fee Amount',
                'flash' => view('flash::message')->render(),
        ]);
        }
    }

    public function edit_fee(Request $request){
        $student_fee = StudentFee::find($request->id);
        $student_fee->received_payment_fee = null;
        $student_fee->status = 'pending';
        $student_fee->fee_charges_left = $student_fee->total_fee;
        $student_fee->save();
         $student_name = $student_fee->student->student_name;
         $statusView = View::make('admin.pages.student_fee.status_view', ['status' => $student_fee->status, 'student' => $student_fee])->render();
         return response()->json(['student' => $student_fee, 'updatedStatusHTML' => $statusView ]);

    }
    public function add_full_fee(Request $request){
        $student_fee = StudentFee::find($request->id);
        $student_fee->received_payment_fee = $student_fee->total_fee;
        $student_fee->status = 'paid';
        $student_fee->fee_charges_left = null;
        $student_fee->save();
        $student_name = $student_fee->student->student_name;
        $statusView = View::make('admin.pages.student_fee.status_view', ['status' => $student_fee->status, 'student' => $student_fee])->render();
        return response()->json(['student' => $student_fee, 'updatedStatusHTML' => $statusView ]);

    }
}
