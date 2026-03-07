<?php

namespace App\Http\Controllers;

use App\Models\GradingSystem;
use App\Models\GradeRule;
use Illuminate\Http\Request;

class GradingSystemController extends Controller
{
    /**
     * List all grading systems.
     */
    public function index()
    {
        $gradingSystems = GradingSystem::with('gradeRules')->get();
        return view('admin.pages.grading.index', compact('gradingSystems'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        return view('admin.pages.grading.create');
    }

    /**
     * Store grading system with grade rules.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'rules'                  => 'required|array|min:1',
            'rules.*.grade'          => 'required|string|max:10',
            'rules.*.min_percentage' => 'required|numeric|min:0|max:100',
            'rules.*.max_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $system = GradingSystem::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_default'  => $request->boolean('is_default'),
            'is_active'   => $request->boolean('is_active', true),
            'school_id'   => auth()->user()->school_id ?? null,
        ]);

        if ($system->is_default) {
            GradingSystem::where('id', '!=', $system->id)->update(['is_default' => false]);
        }

        foreach ($request->rules as $i => $gradeData) {
            GradeRule::create([
                'grading_system_id' => $system->id,
                'grade'             => $gradeData['grade'],
                'grade_label'       => $gradeData['grade_label'] ?? null,
                'min_percentage'    => $gradeData['min_percentage'],
                'max_percentage'    => $gradeData['max_percentage'],
                'grade_point'       => $gradeData['grade_point'] ?? null,
                'remarks'           => $gradeData['remarks'] ?? null,
                'sort_order'        => $i,
            ]);
        }

        return redirect()->route('grading-systems.index')->with('success', 'Grading system created with ' . count($request->rules) . ' grade rules.');
    }

    /**
     * Show a grading system with its rules.
     */
    public function show(GradingSystem $grading_system)
    {
        $gradingSystem = $grading_system->load('gradeRules');
        return view('admin.pages.grading.show', compact('gradingSystem'));
    }

    /**
     * Edit.
     */
    public function edit(GradingSystem $grading_system)
    {
        $gradingSystem = $grading_system->load('gradeRules');
        return view('admin.pages.grading.edit', compact('gradingSystem'));
    }

    /**
     * Update grading system and its rules.
     */
    public function update(Request $request, GradingSystem $grading_system)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'rules'                  => 'required|array|min:1',
            'rules.*.grade'          => 'required|string|max:10',
            'rules.*.min_percentage' => 'required|numeric|min:0|max:100',
            'rules.*.max_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $grading_system->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_default'  => $request->boolean('is_default'),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        if ($grading_system->is_default) {
            GradingSystem::where('id', '!=', $grading_system->id)->update(['is_default' => false]);
        }

        // Delete old rules and recreate
        $grading_system->gradeRules()->delete();

        foreach ($request->rules as $i => $gradeData) {
            GradeRule::create([
                'grading_system_id' => $grading_system->id,
                'grade'             => $gradeData['grade'],
                'grade_label'       => $gradeData['grade_label'] ?? null,
                'min_percentage'    => $gradeData['min_percentage'],
                'max_percentage'    => $gradeData['max_percentage'],
                'grade_point'       => $gradeData['grade_point'] ?? null,
                'remarks'           => $gradeData['remarks'] ?? null,
                'sort_order'        => $i,
            ]);
        }

        return redirect()->route('grading-systems.show', $grading_system)->with('success', 'Grading system updated.');
    }

    /**
     * Delete.
     */
    public function destroy(GradingSystem $grading_system)
    {
        $grading_system->gradeRules()->delete();
        $grading_system->delete();
        return redirect()->route('grading-systems.index')->with('success', 'Grading system deleted.');
    }
}
