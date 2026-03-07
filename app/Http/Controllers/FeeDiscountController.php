<?php

namespace App\Http\Controllers;

use App\Models\FeeDiscount;
use App\Models\StudentFeeDiscount;
use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class FeeDiscountController extends Controller
{
    /**
     * List all discounts/scholarships.
     */
    public function index()
    {
        $discounts = FeeDiscount::with('classRoom')
            ->withCount('studentDiscounts')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.pages.fee_discount.index', compact('discounts'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $classes = ClassRoom::all();
        $discountTypes = FeeDiscount::discountTypes();
        return view('admin.pages.fee_discount.create', compact('classes', 'discountTypes'));
    }

    /**
     * Store.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'applicable_to'  => 'required|string',
        ]);

        FeeDiscount::create($request->all());

        return redirect()->route('fee-discounts.index')->with('success', 'Discount/Scholarship created.');
    }

    /**
     * Edit.
     */
    public function edit(FeeDiscount $feeDiscount)
    {
        $classes = ClassRoom::all();
        $discountTypes = FeeDiscount::discountTypes();
        return view('admin.pages.fee_discount.edit', compact('feeDiscount', 'classes', 'discountTypes'));
    }

    /**
     * Update.
     */
    public function update(Request $request, FeeDiscount $feeDiscount)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $feeDiscount->update($request->all());

        return redirect()->route('fee-discounts.index')->with('success', 'Discount updated.');
    }

    /**
     * Delete.
     */
    public function destroy(FeeDiscount $feeDiscount)
    {
        $feeDiscount->delete();
        return redirect()->route('fee-discounts.index')->with('success', 'Discount deleted.');
    }

    /**
     * Assign discount to a student.
     */
    public function assignStudent(Request $request)
    {
        $request->validate([
            'student_id'      => 'required|exists:students,id',
            'fee_discount_id' => 'required|exists:fee_discounts,id',
        ]);

        $discount = FeeDiscount::find($request->fee_discount_id);

        // Calculate actual amount
        $discountAmount = $request->discount_amount;
        if (!$discountAmount && $discount->discount_type === 'fixed') {
            $discountAmount = $discount->discount_value;
        }

        StudentFeeDiscount::create([
            'student_id'      => $request->student_id,
            'fee_discount_id' => $request->fee_discount_id,
            'discount_amount' => $discountAmount,
            'effective_from'  => $request->effective_from ?? now(),
            'effective_until' => $request->effective_until,
            'remarks'         => $request->remarks,
            'is_active'       => true,
        ]);

        return redirect()->back()->with('success', 'Discount assigned to student.');
    }

    /**
     * Remove discount from student.
     */
    public function removeStudent(StudentFeeDiscount $studentFeeDiscount)
    {
        $studentFeeDiscount->delete();
        return redirect()->back()->with('success', 'Discount removed from student.');
    }
}
