<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasRolesAndPermissions
{
    /* ════════════════════════════════════════════
     *  Relationships
     * ════════════════════════════════════════════ */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    /* ════════════════════════════════════════════
     *  Role Helpers
     * ════════════════════════════════════════════ */

    /**
     * Check if user has a given role (by name).
     */
    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    /**
     * Assign roles to user (by name or ID).
     */
    public function assignRole(...$roles): void
    {
        $ids = Role::whereIn('name', collect($roles)->flatten())->pluck('id');
        $this->roles()->syncWithoutDetaching($ids);
    }

    /**
     * Remove roles from user.
     */
    public function removeRole(...$roles): void
    {
        $ids = Role::whereIn('name', collect($roles)->flatten())->pluck('id');
        $this->roles()->detach($ids);
    }

    /**
     * Sync roles (replace all current roles).
     */
    public function syncRoles(array $roleNames): void
    {
        $ids = Role::whereIn('name', $roleNames)->pluck('id');
        $this->roles()->sync($ids);
    }

    /* ════════════════════════════════════════════
     *  Permission Helpers
     * ════════════════════════════════════════════ */

    /**
     * Get all permissions (role-based + direct).
     */
    public function getAllPermissions()
    {
        // Direct permissions
        $direct = $this->directPermissions()->pluck('name');

        // Role-based permissions
        $rolePerms = Permission::whereHas('roles', function ($q) {
            $q->whereIn('roles.id', $this->roles()->pluck('roles.id'));
        })->pluck('name');

        return $direct->merge($rolePerms)->unique()->values();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Legacy fallback: admin/super_admin in legacy role column
        if (in_array($this->role, ['admin', 'super_admin'])) {
            return true;
        }

        // Check direct permissions
        if ($this->directPermissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Check role-based permissions
        return Permission::where('name', $permission)
            ->whereHas('roles', function ($q) {
                $q->whereIn('roles.id', $this->roles()->pluck('roles.id'));
            })->exists();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Legacy fallback
        if (in_array($this->role, ['admin', 'super_admin'])) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user can access a module (has any permission in that module).
     */
    public function canAccessModule(string $module): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Check direct permissions
        if ($this->directPermissions()->where('module', $module)->exists()) {
            return true;
        }

        // Check role-based permissions
        return Permission::where('module', $module)
            ->whereHas('roles', function ($q) {
                $q->whereIn('roles.id', $this->roles()->pluck('roles.id'));
            })->exists();
    }

    /**
     * Assign direct permissions.
     */
    public function givePermission(...$permissions): void
    {
        $ids = Permission::whereIn('name', collect($permissions)->flatten())->pluck('id');
        $this->directPermissions()->syncWithoutDetaching($ids);
    }

    /**
     * Revoke direct permissions.
     */
    public function revokePermission(...$permissions): void
    {
        $ids = Permission::whereIn('name', collect($permissions)->flatten())->pluck('id');
        $this->directPermissions()->detach($ids);
    }
}
