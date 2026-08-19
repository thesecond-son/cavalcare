<?php

namespace App\Policies;

use App\Models\AddOn;
use App\Models\User;

class AddOnPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, AddOn $addOn): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, AddOn $addOn): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, AddOn $addOn): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, AddOn $addOn): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, AddOn $addOn): bool
    {
        return $user->hasRole('admin');
    }
}