<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_schools'  => School::count(),
            'active_schools' => School::where('status', 'active')->count(),
            'trial_schools'  => School::where('status', 'trial')->count(),
            'total_users'    => User::count(),
        ];
        $schools = School::with('plan')->latest()->take(10)->get();
        return view('super_admin.dashboard', compact('stats', 'schools'));
    }

    // ── Schools CRUD ─────────────────────────────────────────────

    public function schools()
    {
        $schools = School::with('plan')->latest()->paginate(20);
        $plans   = Plan::where('is_active', true)->get();
        return view('super_admin.schools.index', compact('schools', 'plans'));
    }

    public function createSchool()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('super_admin.schools.create', compact('plans'));
    }

    public function storeSchool(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'subdomain'         => 'nullable|string|unique:schools,subdomain|max:50|alpha_dash',
            'plan_id'           => 'nullable|exists:plans,id',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string|max:100',
            'principal_name'    => 'nullable|string|max:255',
            'registration_no'   => 'nullable|string|max:100',
            'whatsapp_number'   => 'nullable|string|max:20',
            'status'            => 'required|in:active,inactive,trial',
            'subscription_start'=> 'nullable|date',
            'subscription_end'  => 'nullable|date|after:subscription_start',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        $school = School::create($data);

        // Auto-create admin user for the school
        if ($request->filled('admin_email')) {
            User::create([
                'name'      => $request->admin_name ?? $data['name'] . ' Admin',
                'email'     => $request->admin_email,
                'password'  => Hash::make($request->admin_password ?? 'password123'),
                'role'      => 'admin',
                'school_id' => $school->id,
            ]);
        }

        return redirect()->route('super_admin.schools')
                         ->with('success', "School '{$school->name}' created successfully.");
    }

    public function editSchool(School $school)
    {
        $plans = Plan::where('is_active', true)->get();
        return view('super_admin.schools.edit', compact('school', 'plans'));
    }

    public function updateSchool(Request $request, School $school)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'subdomain'         => 'nullable|string|max:50|alpha_dash|unique:schools,subdomain,' . $school->id,
            'plan_id'           => 'nullable|exists:plans,id',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string|max:100',
            'principal_name'    => 'nullable|string|max:255',
            'registration_no'   => 'nullable|string|max:100',
            'whatsapp_number'   => 'nullable|string|max:20',
            'status'            => 'required|in:active,inactive,suspended,trial',
            'subscription_start'=> 'nullable|date',
            'subscription_end'  => 'nullable|date',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        $school->update($data);

        return redirect()->route('super_admin.schools')
                         ->with('success', 'School updated successfully.');
    }

    public function destroySchool(School $school)
    {
        $school->delete();
        return back()->with('success', 'School removed.');
    }

    // ── Plans CRUD ───────────────────────────────────────────────

    public function plans()
    {
        $plans = Plan::withCount('schools')->get();
        return view('super_admin.plans.index', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'price'        => 'required|numeric|min:0',
            'max_students' => 'required|integer|min:1',
            'max_teachers' => 'required|integer|min:1',
            'duration_days'=> 'required|integer|min:1',
        ]);
        Plan::create($data);
        return back()->with('success', 'Plan created.');
    }
}
