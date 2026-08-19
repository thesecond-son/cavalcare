<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Booking $Booking): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Booking $Booking): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Booking $Booking): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Booking $Booking): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Booking $Booking): bool
    {
        return $user->hasRole('admin');
    }
}
