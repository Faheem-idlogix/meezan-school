<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }

    public function view(User $user, Exam $exam): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }

    public function update(User $user, Exam $exam): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Exam $exam): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Exam $exam): bool
    {
        return $this->isAdmin($user);
    }
}
