<?php

namespace App\Http\Controllers;

use App\Models\LateFeeRule;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class LateFeeRuleController extends Controller
{
    public function index()
    {
        $rules = LateFeeRule::with('classRoom')->orderBy('class_room_id')->get();
        $classes = ClassRoom::all();
        return view('admin.pages.late_fee.index', compact('rules', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'charge_type'   => 'required|in:fixed,percentage,per_day',
            'charge_amount' => 'required|numeric|min:0',
            'grace_days'    => 'required|integer|min:0',
        ]);

        LateFeeRule::create($request->all());

        return redirect()->route('late-fee-rules.index')->with('success', 'Late fee rule created.');
    }

    public function update(Request $request, LateFeeRule $lateFeeRule)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'charge_type'   => 'required|in:fixed,percentage,per_day',
            'charge_amount' => 'required|numeric|min:0',
        ]);

        $lateFeeRule->update($request->all());

        return redirect()->route('late-fee-rules.index')->with('success', 'Late fee rule updated.');
    }

    public function destroy(LateFeeRule $lateFeeRule)
    {
        $lateFeeRule->delete();
        return redirect()->route('late-fee-rules.index')->with('success', 'Late fee rule deleted.');
    }
}
