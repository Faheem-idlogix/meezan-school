<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher', 'accountant']);
    }

    public function view(User $user, Student $student): bool
    {
        return in_array($user->role, ['admin', 'teacher', 'accountant']);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Student $student): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return $this->isAdmin($user);
    }
}
