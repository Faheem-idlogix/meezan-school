<?php

namespace App\Policies;

use App\Models\Notice;
use App\Models\User;

class NoticePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // all roles can view notices
    }

    public function view(User $user, Notice $notice): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }

    public function update(User $user, Notice $notice): bool
    {
        return $this->isAdmin($user) || $notice->created_by === $user->id;
    }

    public function delete(User $user, Notice $notice): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Notice $notice): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Notice $notice): bool
    {
        return $this->isAdmin($user);
    }
}
