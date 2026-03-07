<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users   = User::with('roles')->get();
        $trashed = User::onlyTrashed()->get();
        return view('admin.pages.users.index', compact('users', 'trashed'));
    }

    public function create()
    {
        $roles = Role::orderBy('display_name')->get();
        return view('admin.pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $role->name, // keep legacy column in sync
        ]);

        $user->assignRole($role->name);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('display_name')->get();
        return view('admin.pages.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $role->name, // keep legacy column in sync
        ];
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        $user->syncRoles([$role->name]);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deactivated');
    }

    public function restore($id)
    {
        User::onlyTrashed()->findOrFail($id)->restore();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'User restored successfully']);
        }
        return redirect()->route('users.index')->with('success', 'User restored successfully');
    }

    public function forceDelete($id)
    {
        User::onlyTrashed()->findOrFail($id)->forceDelete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'User permanently deleted']);
        }
        return redirect()->route('users.index')->with('success', 'User permanently deleted');
    }
}
