<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $vouchers = Voucher::with('student', 'items')->get();
        return view('admin.pages.voucher.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = \App\Models\Student::all();

        $nextVoucherCode = Voucher::max('voucher_code');
        $nextVoucherCode = $nextVoucherCode ? $nextVoucherCode + 1 : 100;

        return view('admin.pages.voucher.create', [
            'students' => $students,
            'nextVoucherCode' => $nextVoucherCode,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'expiry_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_price' => 'required|numeric|min:0',
        ]);

        $voucherCode = Voucher::max('voucher_code');
        $voucherCode = $voucherCode ? $voucherCode + 1 : 100;

        $amount = collect($validatedData['items'])->sum('item_price');

        $voucher = Voucher::create([
            'student_id' => $validatedData['student_id'],
            'voucher_code' => $voucherCode,
            'amount' => $amount,
            'expiry_date' => $validatedData['expiry_date'],
        ]);

        foreach ($validatedData['items'] as $itemData) {
            $voucher->items()->create([
                'item_name' => $itemData['item_name'],
                'item_price' => $itemData['item_price'],
            ]);
        }

        return redirect()->route('voucher.index')->with('success', 'Voucher created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Voucher $voucher)
    {
        //

        $voucher->load('student', 'items'); 
        return Pdf::loadView('admin.pages.voucher.show', compact('voucher'))
    ->setPaper('a4', 'landscape') // Landscape A4
    ->stream('voucher.pdf');
        // return Pdf::loadView('admin.pages.voucher.show', compact('voucher'))->stream('voucher.pdf')->setPaper('a4', 'landscape');   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voucher $voucher)
    {
        $students = \App\Models\Student::all();
        $voucher->load('items');

        return view('admin.pages.voucher.create', [
            'students' => $students,
            'voucher' => $voucher,
            'nextVoucherCode' => $voucher->voucher_code,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voucher $voucher)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'expiry_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_price' => 'required|numeric|min:0',
        ]);

        $amount = collect($validatedData['items'])->sum('item_price');

        $voucher->update([
            'student_id' => $validatedData['student_id'],
            // keep existing voucher_code
            'amount' => $amount,
            'expiry_date' => $validatedData['expiry_date'],
        ]);

        $voucher->items()->delete();
        foreach ($validatedData['items'] as $itemData) {
            $voucher->items()->create([
                'item_name' => $itemData['item_name'],
                'item_price' => $itemData['item_price'],
            ]);
        }

        return redirect()->route('voucher.index')->with('success', 'Voucher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        //
        $voucher->items()->delete();
        $voucher->delete();
        return redirect()->route('voucher.index')->with('success', 'Voucher deleted successfully.');
    }
}
