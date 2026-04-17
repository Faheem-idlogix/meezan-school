<?php

namespace App\Http\Controllers;

use App\Models\ClassFeeVoucher;
use App\Models\StudentFee;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Voucher;
use App\Models\VoucherItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    private int $schoolId = 1;

    // ── Finance Hub (Ledger + Fee Report + Expenses) ─────────────────
    public function index(Request $request)
    {
        $tab       = $request->tab ?? 'ledger';
        $month     = $request->month ?? now()->format('Y-m');
        $type      = $request->type ?? '';  // income / expense / ''

        [$year, $mon] = explode('-', $month);

        // ── Ledger: all vouchers (income + expense) ──────────────────
        $voucherQuery = Voucher::with(['student', 'items'])
            ->where('vouchers.school_id', $this->schoolId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $mon)
            ->orderByDesc('created_at');

        if ($type) $voucherQuery->where('type', $type);

        $vouchers = $voucherQuery->paginate(20)->withQueryString();

        // ── Ledger totals for selected month ─────────────────────────
        $ledgerTotals = Voucher::where('school_id', $this->schoolId)
            ->whereYear('created_at', $year)->whereMonth('created_at', $mon)
            ->selectRaw("SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
                         SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense")
            ->first();

        // ── Fee Report for selected month ─────────────────────────────
        $monthName  = \Carbon\Carbon::createFromDate($year, $mon, 1)->format('F Y');
        $feeRecordsAll = StudentFee::with(['student.classroom'])
            ->where('fee_month', $monthName)
            ->orderBy('student_id')
            ->get();

        $feeRecords = StudentFee::with(['student.classroom'])
            ->where('fee_month', $monthName)
            ->orderBy('student_id')
            ->paginate(20)->withQueryString();

        $feeSummary = [
            'total'      => $feeRecordsAll->sum('total_fee'),
            'received'   => $feeRecordsAll->sum('received_payment_fee'),
            'outstanding'=> $feeRecordsAll->sum('total_fee') - $feeRecordsAll->sum('received_payment_fee'),
            'paid_count' => $feeRecordsAll->where('status', 'paid')->count(),
            'unpaid_count'=> $feeRecordsAll->where('status', 'unpaid')->count(),
        ];

        // ── All-time totals ───────────────────────────────────────────
        $allTotals = Voucher::where('school_id', $this->schoolId)
            ->selectRaw("SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
                         SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense")
            ->first();

        $students = Student::orderBy('student_name')->get();
        $categories = ['Student Fee','Salary','Utility','Rent','Maintenance','Stationery','Event','Other'];

        return view('admin.pages.finance.index', compact(
            'tab', 'month', 'type', 'vouchers', 'ledgerTotals',
            'feeRecords', 'feeSummary', 'allTotals', 'students', 'categories', 'monthName'
        ));
    }

    // ── Store a new journal voucher (income or expense) ──────────────
    public function storeVoucher(Request $request)
    {
        $data = $request->validate([
            'type'         => 'required|in:income,expense',
            'amount'       => 'required|numeric|min:0.01',
            'category'     => 'required|string|max:100',
            'description'  => 'nullable|string|max:500',
            'reference_no' => 'nullable|string|max:100',
            'voucher_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank,cheque,online',
            'student_id'   => 'nullable|exists:students,id',
            'items'        => 'nullable|array',
            'items.*.item_name'  => 'required_with:items|string|max:255',
            'items.*.item_price' => 'required_with:items|numeric|min:0',
        ]);

        $voucherCode = Voucher::max('voucher_code');
        $voucherCode = $voucherCode ? $voucherCode + 1 : 1001;

        $voucher = Voucher::create([
            'school_id'    => $this->schoolId,
            'student_id'   => $data['student_id'] ?? null,
            'voucher_code' => $voucherCode,
            'amount'       => $data['amount'],
            'type'         => $data['type'],
            'category'     => $data['category'],
            'description'  => $data['description'] ?? null,
            'reference_no' => $data['reference_no'] ?? null,
            'voucher_date' => $data['voucher_date'],
            'payment_mode' => $data['payment_mode'],
            'expiry_date'  => now()->addYear(),
        ]);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $voucher->items()->create($item);
            }
        }

        return redirect()->route('finance.index', ['tab' => 'ledger'])
                         ->with('success', ucfirst($data['type']) . ' voucher #' . $voucherCode . ' created.');
    }

    // ── Destroy voucher ───────────────────────────────────────────────
    public function destroyVoucher(Voucher $voucher)
    {
        $voucher->items()->delete();
        $voucher->delete();
        return back()->with('success', 'Voucher deleted.');
    }

    // ── Monthly chart data (AJAX) ─────────────────────────────────────
    public function chartData()
    {
        // Last 6 months fee collection
        $rows = StudentFee::selectRaw('fee_month, SUM(received_payment_fee) as collected, SUM(total_fee) as billed')
            ->groupBy('fee_month')
            ->orderByRaw('MIN(created_at) DESC')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        return response()->json($rows);
    }

    // ── Fee update helpers (called from finance fee tab via AJAX) ─────
    public function feeAddPartial(Request $request)
    {
        $fee = StudentFee::findOrFail($request->id);
        $add = (float) $request->fee;
        $newReceived = ($fee->received_payment_fee ?? 0) + $add;
        $fee->received_payment_fee = $newReceived;
        $fee->status = $newReceived >= $fee->total_fee ? 'paid' : 'pending';
        $fee->save();
        return response()->json(['student' => $fee, 'updatedStatusHTML' => view('admin.pages.student_fee.status_view', ['status' => $fee->status, 'student' => $fee])->render()]);
    }
}
