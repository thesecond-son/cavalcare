<?php

namespace App\Policies;

use App\Models\Caregiver;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CaregiverPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Caregiver $Caregiver): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Caregiver $Caregiver): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Caregiver $Caregiver): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Caregiver $Caregiver): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Caregiver $Caregiver): bool
    {
        return $user->hasRole('admin');
    }
}
