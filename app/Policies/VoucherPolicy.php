<?php

namespace App\Policies;

use App\Models\Voucher;
use App\Models\User;

class VoucherPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function view(User $user, Voucher $voucher): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function update(User $user, Voucher $voucher): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function delete(User $user, Voucher $voucher): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Voucher $voucher): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Voucher $voucher): bool
    {
        return $this->isAdmin($user);
    }
}
