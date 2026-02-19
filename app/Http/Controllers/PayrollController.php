<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollAdvance;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    private int $schoolId = 1;

    // ── Index — list all payrolls ─────────────────────────────────

    public function index(Request $request)
    {
        $month     = $request->month ?? date('n');
        $year      = $request->year  ?? date('Y');
        $teacherId = $request->teacher_id;

        $query = Payroll::with('teacher')
                        ->where('school_id', $this->schoolId)
                        ->where('month', $month)
                        ->where('year', $year);

        if ($teacherId) $query->where('teacher_id', $teacherId);

        $payrolls  = $query->get();
        $teachers  = Teacher::where('school_id', $this->schoolId)->orWhere('school_id', 0)->get();
        $months    = collect(range(1, 12))->map(fn($m) => ['value' => $m, 'label' => Carbon::create()->month($m)->format('F')]);

        // Summary stats for the month
        $stats = [
            'total_gross'    => $payrolls->sum('total_earnings'),
            'total_net'      => $payrolls->sum('net_salary'),
            'total_advances' => $payrolls->sum('advance_deduction'),
            'paid_count'     => $payrolls->where('status', 'paid')->count(),
            'pending_count'  => $payrolls->whereIn('status', ['draft', 'approved'])->count(),
        ];

        return view('admin.pages.payroll.index', compact('payrolls', 'teachers', 'months', 'month', 'year', 'teacherId', 'stats'));
    }

    // ── Create ────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $teachers = Teacher::where('school_id', $this->schoolId)->orWhere('school_id', 0)->get();
        $months   = collect(range(1, 12))->map(fn($m) => ['value' => $m, 'label' => Carbon::create()->month($m)->format('F')]);
        $teacher  = $request->teacher_id ? Teacher::find($request->teacher_id) : null;

        // Auto-fill pending advance for selected teacher
        $pendingAdvance = 0;
        if ($teacher) {
            $pendingAdvance = PayrollAdvance::where('teacher_id', $teacher->id)
                                            ->where('is_deducted', false)
                                            ->sum('amount');
        }

        return view('admin.pages.payroll.create', compact('teachers', 'months', 'teacher', 'pendingAdvance'));
    }

    // ── Store ─────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id'          => 'required|exists:teachers,id',
            'month'               => 'required|integer|between:1,12',
            'year'                => 'required|integer|min:2020',
            'basic_salary'        => 'required|numeric|min:0',
            'house_rent_allowance'=> 'nullable|numeric|min:0',
            'medical_allowance'   => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'bonus'               => 'nullable|numeric|min:0',
            'other_allowances'    => 'nullable|numeric|min:0',
            'advance_deduction'   => 'nullable|numeric|min:0',
            'absence_deduction'   => 'nullable|numeric|min:0',
            'tax_deduction'       => 'nullable|numeric|min:0',
            'other_deductions'    => 'nullable|numeric|min:0',
            'working_days'        => 'nullable|integer|min:0',
            'present_days'        => 'nullable|integer|min:0',
            'absent_days'         => 'nullable|integer|min:0',
            'leave_days'          => 'nullable|integer|min:0',
            'payment_method'      => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);

        // Check duplicate
        $exists = Payroll::where('teacher_id', $data['teacher_id'])
                         ->where('month', $data['month'])
                         ->where('year', $data['year'])
                         ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Payroll for this teacher/month already exists.'])->withInput();
        }

        $data['school_id'] = $this->schoolId;
        $data['status']    = 'draft';
        $payroll = Payroll::create($data);

        // Mark advances as deducted if advance_deduction applied
        if (($data['advance_deduction'] ?? 0) > 0) {
            PayrollAdvance::where('teacher_id', $data['teacher_id'])
                          ->where('is_deducted', false)
                          ->update([
                              'is_deducted'  => true,
                              'deduct_month' => $data['month'],
                              'deduct_year'  => $data['year'],
                          ]);
        }

        return redirect()->route('payroll.index')
                         ->with('success', 'Payroll created. Review & approve below.');
    }

    // ── Show (Payslip) ────────────────────────────────────────────

    public function show(Payroll $payroll)
    {
        $payroll->load('teacher');
        return view('admin.pages.payroll.show', compact('payroll'));
    }

    // ── Approve ───────────────────────────────────────────────────

    public function approve(Payroll $payroll)
    {
        $payroll->update(['status' => 'approved']);
        return back()->with('success', 'Payroll approved.');
    }

    // ── Mark as Paid ──────────────────────────────────────────────

    public function markPaid(Request $request, Payroll $payroll)
    {
        $payroll->update([
            'status'         => 'paid',
            'paid_date'      => $request->paid_date ?? today(),
            'payment_method' => $request->payment_method ?? 'cash',
            'cheque_no'      => $request->cheque_no,
        ]);

        // Send WhatsApp slip notification
        if ($request->send_whatsapp && $payroll->teacher->whatsapp_number) {
            $this->sendWhatsAppSlip($payroll);
        }

        return back()->with('success', 'Payroll marked as paid.');
    }

    // ── Print / PDF Payslip ───────────────────────────────────────

    public function payslipPdf(Payroll $payroll)
    {
        $payroll->load('teacher');
        $pdf = Pdf::loadView('admin.pages.payroll.payslip_pdf', compact('payroll'))
                  ->setPaper('a5', 'portrait');
        return $pdf->download("payslip_{$payroll->teacher->teacher_name}_{$payroll->month}_{$payroll->year}.pdf");
    }

    // ── Destroy ───────────────────────────────────────────────────

    public function destroy(Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            return back()->withErrors(['error' => 'Cannot delete a paid payroll.']);
        }
        $payroll->delete();
        return back()->with('success', 'Payroll record deleted.');
    }

    // ── WhatsApp Helpers ──────────────────────────────────────────

    private function sendWhatsAppSlip(Payroll $payroll): void
    {
        $teacher = $payroll->teacher;
        $msg = urlencode(
            "📋 *Payslip — {$payroll->month_name} {$payroll->year}*\n" .
            "👤 Name: {$teacher->teacher_name}\n" .
            "💰 Basic Salary: Rs. " . number_format($payroll->basic_salary, 0) . "\n" .
            "✅ Total Earnings: Rs. " . number_format($payroll->total_earnings, 0) . "\n" .
            "❌ Deductions: Rs. " . number_format($payroll->total_deductions, 0) . "\n" .
            "💵 *Net Salary: Rs. " . number_format($payroll->net_salary, 0) . "*\n" .
            "📅 Paid on: " . ($payroll->paid_date?->format('d M Y') ?? today()->format('d M Y'))
        );

        $phone = preg_replace('/[^0-9]/', '', $teacher->whatsapp_number);
        if (str_starts_with($phone, '0')) $phone = '92' . substr($phone, 1);

        $payroll->update(['whatsapp_sent' => true, 'whatsapp_sent_at' => now()]);
    }

    // ── Advances ──────────────────────────────────────────────────

    public function advances()
    {
        $advances = PayrollAdvance::with('teacher')
                                  ->where('school_id', $this->schoolId)
                                  ->latest()
                                  ->paginate(25);
        $teachers = Teacher::where('school_id', $this->schoolId)->orWhere('school_id', 0)->get();
        return view('admin.pages.payroll.advances', compact('advances', 'teachers'));
    }

    public function storeAdvance(Request $request)
    {
        $data = $request->validate([
            'teacher_id'   => 'required|exists:teachers,id',
            'amount'       => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'reason'       => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);
        $data['school_id'] = $this->schoolId;
        PayrollAdvance::create($data);
        return back()->with('success', 'Advance recorded successfully.');
    }
}
