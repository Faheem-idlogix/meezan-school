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
        $class_fee_voucher = ClassFeeVoucher::with('classroom')
            ->whereHas('classroom')
            ->orderBy('class_fee_voucher_id', 'desc')->get();
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
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'months'        => 'required|array|min:1',
            'months.*'      => 'required|string',
            'issue_date'    => 'nullable|date',
            'submit_date'   => 'nullable|date',
        ], [
            'months.required' => 'Please select at least one month for the voucher.',
        ]);

        // Parse + sort selected months chronologically
        $monthsRaw = array_values(array_unique($request->input('months', [])));
        $monthsParsed = collect($monthsRaw)
            ->map(fn ($m) => Carbon::createFromFormat('F Y', trim($m))->startOfMonth())
            ->sort()
            ->values();
        $sortedMonths = $monthsParsed->map(fn ($d) => $d->format('F Y'))->all();

        // Build the human-readable label shown on the voucher
        if (count($sortedMonths) === 1) {
            $fee_month = $sortedMonths[0];
        } else {
            // Detect a contiguous range in the same year for a compact label e.g. "Jun - Aug 2026"
            $isContiguous = true;
            for ($i = 1; $i < $monthsParsed->count(); $i++) {
                if (! $monthsParsed[$i - 1]->copy()->addMonth()->equalTo($monthsParsed[$i])) {
                    $isContiguous = false;
                    break;
                }
            }
            if ($isContiguous && $monthsParsed->first()->year === $monthsParsed->last()->year) {
                $fee_month = $monthsParsed->first()->format('M') . ' - ' . $monthsParsed->last()->format('M Y');
            } else {
                $fee_month = implode(', ', $sortedMonths);
            }
        }

        // Carry-forward arrears are based on the month BEFORE the earliest selected month
        $lastMonthFormatted = $monthsParsed->first()->copy()->subMonth()->format('F Y');

        $issueDate  = $request->issue_date ?: Carbon::now()->toDateString();
        $submitDate = $request->submit_date;

        $classroom    = ClassRoom::find($request->class_room_id);
        $class_name   = $classroom->class_name ?? '';
        $section_name = $classroom->section_name ?? '';

        $students = Student::where('class_room_id', $request->class_room_id)
            ->whereNull('deleted_at')->get();

        $monthCount = count($sortedMonths);

        // Academic (tuition) fee is per-month, so multiply by selected month count.
        // Other charges are treated as one-time totals entered by the admin.
        $academic_fee_total = ((int) $request->academic_fee) * $monthCount;

        $total_fee = $academic_fee_total
            + (int) $request->stationery_charges
            + (int) $request->test_series_charges
            + (int) $request->exam_charges
            + (int) $request->notebook_charges
            + (int) $request->book_charges
            + (int) $request->fine
            + (int) $request->arrears;

        $fee_voucher_name = trim($class_name . '-' . $section_name) . ' ' . $fee_month;

        $class_fee_voucher = new ClassFeeVoucher();
        $class_fee_voucher->name          = $fee_voucher_name;
        $class_fee_voucher->month         = $fee_month;
        $class_fee_voucher->class_room_id = $request->class_room_id;
        $class_fee_voucher->save();
        $class_fee_voucher_id = $class_fee_voucher->class_fee_voucher_id;

        foreach ($students as $student) {
            $last_month_charges = StudentFee::where('student_id', $student->id)
                ->where('fee_month', $lastMonthFormatted)
                ->value('fee_charges_left') ?? 0;

            $student_fee = new StudentFee();
            $student_fee->student_id           = $student->id;
            $student_fee->class_fee_voucher_id = $class_fee_voucher_id;
            $student_fee->voucher_no           = StudentFee::generateUniqueVoucherNumber();
            $student_fee->fee_month            = $fee_month;
            $student_fee->issue_date           = $issueDate;
            $student_fee->submit_date          = $submitDate;
            $student_fee->total_fee            = $total_fee + $last_month_charges;
            $student_fee->fee_charges_left     = $total_fee + $last_month_charges;
            $student_fee->stationery_charges   = $request->stationery_charges;
            $student_fee->test_series_charges  = $request->test_series_charges;
            $student_fee->exam_charges         = $request->exam_charges;
            $student_fee->notebook_charges     = $request->notebook_charges;
            $student_fee->book_charges         = $request->book_charges;
            $student_fee->fine                 = $request->fine;
            $student_fee->arrears              = ((int) $request->arrears) + $last_month_charges;
            $student_fee->academic_fee         = $academic_fee_total;
            $student_fee->note                 = $request->note;
            $student_fee->status               = 'unpaid';
            $student_fee->save();
        }

        return redirect()->back()->with(
            'success',
            'Fee Voucher Created successfully for: ' . $fee_month
        );
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
       
        $data = StudentFee::with('class_fee_voucher', 'student.classroom')->where('class_fee_voucher_id', $id)
        ->whereHas('student', function ($q) {
            $q->whereNull('deleted_at');
        })->get();

        if ($data->isEmpty()) {
            return redirect()->route('fee_voucher')
                ->with('error', 'No active students found for this voucher. All students may have been deleted.');
        }

        $view = setting('invoice_layout', 'basic') === 'detailed'
            ? 'admin.report.student_fee_advanced'
            : 'admin.report.student_fee';

        $pdf = PDF::loadView($view, ['data' => $data])
                  ->setPaper('a4', 'landscape');
        $pdf->render();
    
        return $pdf->stream('voucher.pdf');
    }
}
