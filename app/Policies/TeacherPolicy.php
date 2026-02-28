<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return in_array($user->role, ['admin', 'accountant']);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Teacher $teacher): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Teacher $teacher): bool
    {
        return $this->isAdmin($user);
    }
}
