<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Base policy — admin & super_admin can do everything.
 * Extend this for model-specific rules.
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Super-admin bypasses all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }
        return null;
    }

    protected function isAdmin(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    protected function isTeacher(User $user): bool
    {
        return $user->role === 'teacher';
    }

    protected function isAccountant(User $user): bool
    {
        return $user->role === 'accountant';
    }
}
