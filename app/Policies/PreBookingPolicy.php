<?php

namespace App\Policies;

use App\Models\PreBooking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PreBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, PreBooking $PreBooking): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, PreBooking $PreBooking): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, PreBooking $PreBooking): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, PreBooking $PreBooking): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, PreBooking $PreBooking): bool
    {
        return $user->hasRole('admin');
    }
}
