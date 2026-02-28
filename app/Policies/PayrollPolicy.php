<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;

class PayrollPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function view(User $user, Payroll $payroll): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function update(User $user, Payroll $payroll): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Payroll $payroll): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Payroll $payroll): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Payroll $payroll): bool
    {
        return $this->isAdmin($user);
    }
}
