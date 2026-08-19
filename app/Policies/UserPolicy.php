<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, User $User): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $User): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, User $User): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, User $User): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, User $User): bool
    {
        return $user->hasRole('admin');
    }
}
