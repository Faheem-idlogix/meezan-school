<?php

namespace App\Http\Controllers;

use App\Models\ClassFeeVoucher;
use App\Models\ClassRoom;
use App\Models\Session;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Carbon\Carbon;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;




class ClassFeeVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $class_fee_voucher = ClassFeeVoucher::orderBy('class_fee_voucher_id', 'desc')->get();
        return view('admin.pages.invoice.index', compact('class_fee_voucher'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $class_room = ClassRoom::all();
        $session = Session::all();

        return view('admin.pages.invoice.create', compact('class_room','session'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $currentDate = Carbon::now();
        $lastMonthFormatted = $currentDate->subMonth()->format('F Y');

        $class_name = ClassRoom::where('id', $request->class_room_id)->value('class_name');
        $section_name = ClassRoom::where('id', $request->class_room_id)->value('section_name');
        $students = Student::where('class_room_id', $request->class_room_id)->get();
        $total_fee = $request->stationery_charges +
        $request->test_series_charges +
        $request->exam_charges +
        $request->notebook_charges +
        $request->book_charges +
        $request->fine +
        $request->arrears +
        $request->academic_fee;
        $carbonDate = Carbon::parse($request->issue_date);
        $fee_month = $carbonDate->format('F Y'); 
        $fee_voucher_name =  $class_name.'-'.$section_name.' '. $fee_month;
        $class_fee_voucher = new ClassFeeVoucher();
        $class_fee_voucher->name = $fee_voucher_name;
        $class_fee_voucher->month = $fee_month;
        $class_fee_voucher->class_room_id = $request->class_room_id;
        $class_fee_voucher->save();
        $class_fee_voucher_id = ClassFeeVoucher::max('class_fee_voucher_id');

        foreach ($students as $student){
            $student_fee = new StudentFee();
            $last_month_charges = StudentFee::where('student_id', $student->id)->where('fee_month', $lastMonthFormatted)->value('fee_charges_left') ?? 0;
            $student_fee->student_id = $student->id;
            $student_fee->class_fee_voucher_id =  $class_fee_voucher_id;
            $student_fee->voucher_no = StudentFee::generateUniqueVoucherNumber();
            $student_fee->fee_month = $fee_month;
            $student_fee->issue_date = $request->issue_date;
            $student_fee->submit_date = $request->submit_date;
            $student_fee->total_fee = $total_fee + $last_month_charges;
            $student_fee->stationery_charges = $request->stationery_charges;
            $student_fee->test_series_charges = $request->test_series_charges;
            $student_fee->exam_charges = $request->exam_charges;
            $student_fee->notebook_charges = $request->notebook_charges;
            $student_fee->book_charges = $request->book_charges;
            $student_fee->fine = $request->fine;
            $student_fee->arrears = $request->arrears  + $last_month_charges;
            $student_fee->academic_fee = $request->academic_fee;
            $student_fee->note = $request->note;
            $student_fee->status = 'unpaid';
            $student_fee->save();
        }

        return redirect()->back()->with('success','Fee Voucher Created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassFeeVoucher $classFeeVoucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassFeeVoucher $classFeeVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassFeeVoucher $classFeeVoucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassFeeVoucher $classFeeVoucher, $id)
    {
        //
        $classroom = classFeeVoucher::find($id);
        $classroom->delete();
        return redirect()->route('fee_voucher')->with('success','Voucher deleted successfully');
    }

    public function generate_fee_voucher($id){
       
        $data = StudentFee::with('class_fee_voucher', 'student.classroom')->where('class_fee_voucher_id', $id)->get();
        $pdf = PDF::loadView('admin.report.student_fee',  ['data' => $data])
                  ->setPaper('a4', 'landscape'); // make it horizontal  
        $pdf->render();
    
        // Output the generated PDF to the browser or save it to a file
        return $pdf->stream('voucher.pdf');
        // return $pdf->download('itsolutionstuff.pdf');    }
          }
}
