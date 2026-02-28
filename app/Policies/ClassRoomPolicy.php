<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\User;

class ClassRoomPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher', 'accountant']);
    }

    public function view(User $user, ClassRoom $classRoom): bool
    {
        return in_array($user->role, ['admin', 'teacher', 'accountant']);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, ClassRoom $classRoom): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, ClassRoom $classRoom): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, ClassRoom $classRoom): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, ClassRoom $classRoom): bool
    {
        return $this->isAdmin($user);
    }
}
