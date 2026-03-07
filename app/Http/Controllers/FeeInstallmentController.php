<?php

namespace App\Http\Controllers;

use App\Models\FeeInstallmentPlan;
use App\Models\FeeInstallment;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeeInstallmentController extends Controller
{
    /**
     * List all installment plans.
     */
    public function index()
    {
        $plans = FeeInstallmentPlan::with('student.classroom', 'installments')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_plans' => $plans->count(),
            'active'      => $plans->where('status', 'active')->count(),
            'completed'   => $plans->where('status', 'completed')->count(),
            'defaulted'   => $plans->where('status', 'defaulted')->count(),
        ];

        return view('admin.pages.fee_installment.index', compact('plans', 'stats'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $students = Student::with('classroom')->orderBy('student_name')->get();
        return view('admin.pages.fee_installment.create', compact('students'));
    }

    /**
     * Store a new installment plan and auto-generate installments.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'              => 'required|exists:students,id',
            'total_amount'            => 'required|numeric|min:1',
            'number_of_installments'  => 'required|integer|min:2|max:24',
            'start_date'              => 'required|date',
        ]);

        $totalAmount = $request->total_amount;
        $numInstallments = $request->number_of_installments;
        $installmentAmount = round($totalAmount / $numInstallments, 2);

        $plan = FeeInstallmentPlan::create([
            'student_id'             => $request->student_id,
            'plan_name'              => $request->plan_name ?? 'Installment Plan',
            'total_amount'           => $totalAmount,
            'number_of_installments' => $numInstallments,
            'installment_amount'     => $installmentAmount,
            'start_date'             => $request->start_date,
            'status'                 => 'active',
            'remarks'                => $request->remarks,
            'created_by'             => auth()->id(),
        ]);

        // Auto-generate installment records
        $dueDate = Carbon::parse($request->start_date);
        for ($i = 1; $i <= $numInstallments; $i++) {
            FeeInstallment::create([
                'fee_installment_plan_id' => $plan->id,
                'installment_number'      => $i,
                'amount'                  => $installmentAmount,
                'due_date'                => $dueDate->copy(),
                'status'                  => 'pending',
            ]);
            $dueDate->addMonth();
        }

        return redirect()->route('fee-installments.show', $plan)->with('success', 'Installment plan created with ' . $numInstallments . ' installments.');
    }

    /**
     * Show plan detail with all installments.
     */
    public function show(FeeInstallmentPlan $feeInstallment)
    {
        $feeInstallment->load('student.classroom', 'installments', 'createdByUser');
        return view('admin.pages.fee_installment.show', compact('feeInstallment'));
    }

    /**
     * Record payment for an installment.
     */
    public function recordPayment(Request $request, FeeInstallment $feeInstallment)
    {
        $request->validate([
            'paid_amount'    => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
        ]);

        $paidAmount = $request->paid_amount;
        $status = ($paidAmount >= $feeInstallment->amount) ? 'paid' : 'partial';

        $feeInstallment->update([
            'paid_amount'    => $paidAmount,
            'paid_date'      => now(),
            'status'         => $status,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'remarks'        => $request->remarks,
        ]);

        // Check if all installments are paid → complete plan
        $plan = $feeInstallment->plan;
        if ($plan->installments()->where('status', '!=', 'paid')->count() === 0) {
            $plan->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Payment recorded for installment #' . $feeInstallment->installment_number);
    }

    /**
     * Delete a plan.
     */
    public function destroy(FeeInstallmentPlan $feeInstallment)
    {
        $feeInstallment->installments()->delete();
        $feeInstallment->delete();
        return redirect()->route('fee-installments.index')->with('success', 'Installment plan deleted.');
    }
}
