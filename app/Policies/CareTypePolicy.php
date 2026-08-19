<?php

namespace App\Policies;

use App\Models\CareType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CareTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, CareType $CareType): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, CareType $CareType): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, CareType $CareType): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, CareType $CareType): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, CareType $CareType): bool
    {
        return $user->hasRole('admin');
    }
}
