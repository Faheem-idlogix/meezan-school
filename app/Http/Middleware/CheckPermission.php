<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Usage:  Route::middleware('permission:students.view')
     *         Route::middleware('permission:students.view,students.create')  ← any
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Gracefully skip permission checks if RBAC tables haven't been migrated yet
        try {
            // Super admin bypasses everything
            if ($user->hasRole('super_admin')) {
                return $next($request);
            }

            // Legacy fallback: admin role from string column bypasses too
            if (in_array($user->role, ['admin', 'super_admin'])) {
                return $next($request);
            }

            // Check if user has any of the required permissions
            if (!$user->hasAnyPermission($permissions)) {
                abort(403, 'You do not have permission to access this resource.');
            }
        } catch (\Exception $e) {
            // If RBAC tables don't exist yet, fall back to legacy admin check
            if (in_array($user->role, ['admin', 'super_admin'])) {
                return $next($request);
            }
            abort(403, 'Permissions system not configured. Contact administrator.');
        }

        return $next($request);
    }
}
