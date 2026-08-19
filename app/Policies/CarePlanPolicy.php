<?php

namespace App\Policies;

use App\Models\CarePlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CarePlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, CarePlan $CarePlan): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, CarePlan $CarePlan): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, CarePlan $CarePlan): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, CarePlan $CarePlan): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, CarePlan $CarePlan): bool
    {
        return $user->hasRole('admin');
    }
}
