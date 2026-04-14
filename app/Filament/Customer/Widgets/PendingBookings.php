<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Booking;

class PendingBookings extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Bookings',
            Booking::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->count()
        )
        ->description('Awaiting confirmation')
        ->color('warning'),
        ];
    }
}
