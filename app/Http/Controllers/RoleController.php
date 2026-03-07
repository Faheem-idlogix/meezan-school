<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * List all roles with permission counts.
     */
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->get();
        return view('admin.pages.roles.index', compact('roles'));
    }

    /**
     * Create form — show all permissions grouped by module.
     */
    public function create()
    {
        $permissionGroups = Permission::all()->groupBy('group');
        return view('admin.pages.roles.create', compact('permissionGroups'));
    }

    /**
     * Store a new role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'permissions'  => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'         => $request->name,
            'display_name' => $request->display_name,
            'description'  => $request->description,
            'is_system'    => false,
        ]);

        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role "' . $role->display_name . '" created with ' . count($request->permissions ?? []) . ' permissions.');
    }

    /**
     * Show role detail with all permissions.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        $permissionGroups = $role->permissions->groupBy('group');
        return view('admin.pages.roles.show', compact('role', 'permissionGroups'));
    }

    /**
     * Edit form.
     */
    public function edit(Role $role)
    {
        $allPermissions = Permission::all()->groupBy('group');
        $rolePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
        return view('admin.pages.roles.edit', compact('role', 'allPermissions', 'rolePermissionIds'));
    }

    /**
     * Update role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'permissions'  => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Don't allow renaming system roles
        if (!$role->is_system) {
            $request->validate([
                'name' => 'required|string|max:50|unique:roles,name,' . $role->id . '|regex:/^[a-z_]+$/',
            ]);
            $role->name = $request->name;
        }

        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role "' . $role->display_name . '" updated.');
    }

    /**
     * Delete a role (only non-system roles).
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role with assigned users. Reassign users first.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }
}
