<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\ClassRoom;
use App\Models\Session;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    /**
     * Display fee structures grouped by class.
     */
    public function index(Request $request)
    {
        $query = FeeStructure::with('classRoom', 'session')->orderBy('class_room_id');

        if ($request->class_room_id) {
            $query->where('class_room_id', $request->class_room_id);
        }

        $feeStructures = $query->get();
        $grouped = $feeStructures->groupBy(fn($s) => $s->classRoom->class_name ?? 'Unassigned');
        $classes = ClassRoom::all();
        $sessions = Session::all();
        $categories = FeeStructure::feeCategories();

        // Summary stats
        $stats = [
            'total_structures' => $feeStructures->count(),
            'active'           => $feeStructures->where('is_active', true)->count(),
            'classes_covered'  => $feeStructures->pluck('class_room_id')->unique()->count(),
        ];

        return view('admin.pages.fee_structure.index', compact('feeStructures', 'grouped', 'classes', 'sessions', 'categories', 'stats'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $classes = ClassRoom::all();
        $sessions = Session::all();
        $categories = FeeStructure::feeCategories();
        return view('admin.pages.fee_structure.create', compact('classes', 'sessions', 'categories'));
    }

    /**
     * Store.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'fee_category'  => 'required|string',
            'fee_name'      => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'frequency'     => 'required|in:monthly,quarterly,semi_annual,annual,one_time',
        ]);

        FeeStructure::create($request->all());

        return redirect()->route('fee-structures.index')->with('success', 'Fee structure created successfully.');
    }

    /**
     * Edit.
     */
    public function edit(FeeStructure $feeStructure)
    {
        $classes = ClassRoom::all();
        $sessions = Session::all();
        $categories = FeeStructure::feeCategories();
        return view('admin.pages.fee_structure.edit', compact('feeStructure', 'classes', 'sessions', 'categories'));
    }

    /**
     * Update.
     */
    public function update(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'class_room_id' => 'required|exists:class_rooms,id',
            'fee_category'  => 'required|string',
            'fee_name'      => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'frequency'     => 'required|in:monthly,quarterly,semi_annual,annual,one_time',
        ]);

        $feeStructure->update($request->all());

        return redirect()->route('fee-structures.index')->with('success', 'Fee structure updated.');
    }

    /**
     * Delete.
     */
    public function destroy(FeeStructure $feeStructure)
    {
        $feeStructure->delete();
        return redirect()->route('fee-structures.index')->with('success', 'Fee structure deleted.');
    }

    /**
     * AJAX: Get fee breakdown for a class.
     */
    public function getClassFees(Request $request)
    {
        $structures = FeeStructure::where('class_room_id', $request->class_room_id)
            ->active()
            ->get();

        $total = $structures->sum('amount');

        return response()->json([
            'structures' => $structures,
            'total'      => $total,
        ]);
    }
}
