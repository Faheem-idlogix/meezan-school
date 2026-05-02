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
        $monthsRaw    = array_values(array_unique($request->input('months', [])));
        $monthsParsed = collect($monthsRaw)
            ->map(fn ($m) => Carbon::createFromFormat('F Y', trim($m))->startOfMonth())
            ->sort()
            ->values();

        $issueDate    = $request->issue_date ?: Carbon::now()->toDateString();
        $submitDate   = $request->submit_date;

        $classroom    = ClassRoom::find($request->class_room_id);
        $class_name   = $classroom->class_name ?? '';
        $section_name = $classroom->section_name ?? '';

        $students = Student::where('class_room_id', $request->class_room_id)
            ->whereNull('deleted_at')->get();

        // Per-month base charges (academic per month; one-time charges only on first month)
        $academic_fee = (int) $request->academic_fee;

        $createdMonths = [];
        $skippedMonths = [];

        foreach ($monthsParsed as $idx => $monthDate) {
            $fee_month = $monthDate->format('F Y');

            // Skip if a class voucher already exists for this class & month
            $existing = ClassFeeVoucher::where('class_room_id', $request->class_room_id)
                ->where('month', $fee_month)
                ->first();
            if ($existing) {
                $skippedMonths[] = $fee_month;
                continue;
            }

            // One-time charges only applied to the first selected month
            $isFirst                 = ($idx === 0);
            $stationery_charges      = $isFirst ? (int) $request->stationery_charges     : 0;
            $test_series_charges     = $isFirst ? (int) $request->test_series_charges    : 0;
            $exam_charges            = $isFirst ? (int) $request->exam_charges           : 0;
            $notebook_charges        = $isFirst ? (int) $request->notebook_charges       : 0;
            $book_charges            = $isFirst ? (int) $request->book_charges           : 0;
            $fine_charge             = $isFirst ? (int) $request->fine                   : 0;
            $admin_arrears           = $isFirst ? (int) $request->arrears                : 0;

            $base_total = $academic_fee
                        + $stationery_charges
                        + $test_series_charges
                        + $exam_charges
                        + $notebook_charges
                        + $book_charges
                        + $fine_charge
                        + $admin_arrears;

            $fee_voucher_name = trim($class_name . '-' . $section_name) . ' ' . $fee_month;

            $class_fee_voucher = new ClassFeeVoucher();
            $class_fee_voucher->name          = $fee_voucher_name;
            $class_fee_voucher->month         = $fee_month;
            $class_fee_voucher->class_room_id = $request->class_room_id;
            $class_fee_voucher->save();
            $class_fee_voucher_id = $class_fee_voucher->class_fee_voucher_id;

            // Carry forward unpaid balance from the immediately previous month
            $prevMonth = $monthDate->copy()->subMonth()->format('F Y');

            foreach ($students as $student) {
                $last_month_charges = StudentFee::where('student_id', $student->id)
                    ->where('fee_month', $prevMonth)
                    ->value('fee_charges_left') ?? 0;

                $total_fee = $base_total + $last_month_charges;

                $student_fee = new StudentFee();
                $student_fee->student_id           = $student->id;
                $student_fee->class_fee_voucher_id = $class_fee_voucher_id;
                $student_fee->voucher_no           = StudentFee::generateUniqueVoucherNumber();
                $student_fee->fee_month            = $fee_month;
                $student_fee->issue_date           = $issueDate;
                $student_fee->submit_date          = $submitDate;
                $student_fee->total_fee            = $total_fee;
                $student_fee->fee_charges_left     = $total_fee;
                $student_fee->stationery_charges   = $stationery_charges;
                $student_fee->test_series_charges  = $test_series_charges;
                $student_fee->exam_charges         = $exam_charges;
                $student_fee->notebook_charges     = $notebook_charges;
                $student_fee->book_charges         = $book_charges;
                $student_fee->fine                 = $fine_charge;
                $student_fee->arrears              = $admin_arrears + $last_month_charges;
                $student_fee->academic_fee         = $academic_fee;
                $student_fee->note                 = $request->note;
                $student_fee->status               = 'unpaid';
                $student_fee->save();
            }

            $createdMonths[] = $fee_month;
        }

        $msg = 'Voucher created for: ' . (empty($createdMonths) ? 'none' : implode(', ', $createdMonths));
        if (!empty($skippedMonths)) {
            $msg .= '. Skipped (already exists): ' . implode(', ', $skippedMonths);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassFeeVoucher $classFeeVoucher)
    {
        //
    }

    /**
     * Parse any stored fee_month / voucher.month label into an ordered list of
     * Carbon month-starts. Handles:
     *   • "May 2026"                — single month
     *   • "May 2026, June 2026"     — comma list
     *   • "May - Aug 2026"          — compact range, same year
     */
    private function parseMonthLabel(?string $label): array
    {
        if (!$label) return [];
        $label = trim($label);
        $out = [];

        // Comma list
        if (str_contains($label, ',')) {
            foreach (explode(',', $label) as $part) {
                try { $out[] = Carbon::createFromFormat('F Y', trim($part))->startOfMonth(); }
                catch (\Throwable $e) {}
            }
        }
        // Compact range "MMM - MMM YYYY" / "MMMM - MMMM YYYY"
        elseif (preg_match('/^([A-Za-z]+)\s*-\s*([A-Za-z]+)\s+(\d{4})$/', $label, $m)) {
            try {
                $start = Carbon::createFromFormat('M Y', substr($m[1], 0, 3) . ' ' . $m[3])->startOfMonth();
                $end   = Carbon::createFromFormat('M Y', substr($m[2], 0, 3) . ' ' . $m[3])->startOfMonth();
                while ($start->lte($end)) {
                    $out[] = $start->copy();
                    $start->addMonth();
                }
            } catch (\Throwable $e) {}
        }
        // Single
        else {
            try { $out[] = Carbon::createFromFormat('F Y', $label)->startOfMonth(); }
            catch (\Throwable $e) {}
        }

        // Sort & dedupe
        $sorted = collect($out)->unique(fn($d) => $d->format('Y-m'))->sort()->values()->all();
        return $sorted;
    }

    /**
     * Build a compact label for a list of Carbon month-starts:
     *   1 month        → "May 2026"
     *   contiguous     → "May - Jul 2026"
     *   non-contiguous → "May 2026, July 2026"
     */
    private function buildMonthLabel(array $months): string
    {
        if (empty($months)) return '';
        if (count($months) === 1) return $months[0]->format('F Y');

        $contiguous = true;
        for ($i = 1; $i < count($months); $i++) {
            if (!$months[$i - 1]->copy()->addMonth()->equalTo($months[$i])) { $contiguous = false; break; }
        }
        if ($contiguous && $months[0]->year === end($months)->year) {
            return $months[0]->format('M') . ' - ' . end($months)->format('M Y');
        }
        return collect($months)->map(fn($d) => $d->format('F Y'))->implode(', ');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $classFeeVoucher = ClassFeeVoucher::with('classroom')->findOrFail($id);

        // Use any one StudentFee row as a template for the shared charges
        $template = StudentFee::where('class_fee_voucher_id', $id)->first();

        // Currently selected months (parsed from the stored label)
        $selectedMonths = collect($this->parseMonthLabel($classFeeVoucher->month))
            ->map(fn($d) => $d->format('F Y'))->all();

        // Build a 24-month option window centred on the current selection
        $anchor = $selectedMonths
            ? Carbon::createFromFormat('F Y', $selectedMonths[0])
            : Carbon::now();
        $startMonth = $anchor->copy()->subMonths(6)->startOfMonth();

        $monthOptions = [];
        for ($i = 0; $i < 24; $i++) {
            $m = $startMonth->copy()->addMonths($i);
            $monthOptions[$m->format('F Y')] = $m->format('F Y');
        }
        // Ensure currently-selected months are present in options
        foreach ($selectedMonths as $sm) {
            if (!isset($monthOptions[$sm])) $monthOptions[$sm] = $sm;
        }

        return view('admin.pages.invoice.edit', compact(
            'classFeeVoucher', 'template', 'selectedMonths', 'monthOptions'
        ));
    }

    /**
     * Update the specified resource in storage. Applies the new charges
     * to every StudentFee linked to this class voucher (preserving received
     * payments and recalculating outstanding balance).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'months'               => 'nullable|array',
            'months.*'             => 'string',
            'academic_fee'         => 'nullable|numeric',
            'stationery_charges'   => 'nullable|numeric',
            'test_series_charges'  => 'nullable|numeric',
            'exam_charges'         => 'nullable|numeric',
            'notebook_charges'     => 'nullable|numeric',
            'book_charges'         => 'nullable|numeric',
            'fine'                 => 'nullable|numeric',
            'arrears'              => 'nullable|numeric',
            'issue_date'           => 'nullable|date',
            'submit_date'          => 'nullable|date',
        ]);

        $classFeeVoucher = ClassFeeVoucher::findOrFail($id);

        // Resolve final month list (use submitted months; fallback to current)
        $monthsParsed = collect($request->input('months', []))
            ->map(function ($m) {
                try { return Carbon::createFromFormat('F Y', trim($m))->startOfMonth(); }
                catch (\Throwable $e) { return null; }
            })
            ->filter()
            ->unique(fn($d) => $d->format('Y-m'))
            ->sort()
            ->values()
            ->all();

        if (empty($monthsParsed)) {
            $monthsParsed = $this->parseMonthLabel($classFeeVoucher->month);
        }

        $newLabel = $this->buildMonthLabel($monthsParsed) ?: $classFeeVoucher->month;

        // Update voucher header
        $classroom    = $classFeeVoucher->classroom;
        $class_name   = $classroom->class_name ?? '';
        $section_name = $classroom->section_name ?? '';
        $classFeeVoucher->name  = trim($class_name . '-' . $section_name) . ' ' . $newLabel;
        $classFeeVoucher->month = $newLabel;
        $classFeeVoucher->save();

        $academic_fee         = (int) $request->academic_fee;
        $stationery_charges   = (int) $request->stationery_charges;
        $test_series_charges  = (int) $request->test_series_charges;
        $exam_charges         = (int) $request->exam_charges;
        $notebook_charges     = (int) $request->notebook_charges;
        $book_charges         = (int) $request->book_charges;
        $fine_charge          = (int) $request->fine;
        $admin_arrears        = (int) $request->arrears;

        $base_total = $academic_fee
                    + $stationery_charges
                    + $test_series_charges
                    + $exam_charges
                    + $notebook_charges
                    + $book_charges
                    + $fine_charge
                    + $admin_arrears;

        $studentFees = StudentFee::where('class_fee_voucher_id', $id)->get();

        // Earliest month is used as the basis for previous-month carry-forward
        $earliest = !empty($monthsParsed) ? $monthsParsed[0] : null;
        $prevMonth = $earliest ? $earliest->copy()->subMonth()->format('F Y') : null;

        foreach ($studentFees as $fee) {
            $carry = $prevMonth
                ? (StudentFee::where('student_id', $fee->student_id)
                    ->where('fee_month', $prevMonth)
                    ->value('fee_charges_left') ?? 0)
                : 0;

            $total_fee   = $base_total + $carry;
            $received    = (int) ($fee->received_payment_fee ?? 0);
            $left        = max(0, $total_fee - $received);

            $fee->fee_month           = $newLabel;
            $fee->academic_fee        = $academic_fee;
            $fee->stationery_charges  = $stationery_charges;
            $fee->test_series_charges = $test_series_charges;
            $fee->exam_charges        = $exam_charges;
            $fee->notebook_charges    = $notebook_charges;
            $fee->book_charges        = $book_charges;
            $fee->fine                = $fine_charge;
            $fee->arrears             = $admin_arrears + $carry;
            $fee->total_fee           = $total_fee;
            $fee->fee_charges_left    = $left;
            if ($request->filled('issue_date'))  $fee->issue_date  = $request->issue_date;
            if ($request->filled('submit_date')) $fee->submit_date = $request->submit_date;
            if ($request->filled('note'))        $fee->note        = $request->note;

            if ($received <= 0) {
                $fee->status = 'unpaid';
            } elseif ($received >= $total_fee) {
                $fee->status = 'paid';
            } else {
                $fee->status = 'pending';
            }

            $fee->save();
        }

        return redirect()->route('fee_voucher')
            ->with('success', 'Voucher updated for ' . $studentFees->count() . ' student(s) — ' . $newLabel);
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
