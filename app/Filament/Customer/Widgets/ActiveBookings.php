<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Booking;

class ActiveBookings extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Care Plans',
            Booking::where('user_id', auth()->id())
                ->where('status', 'confirmed')
                ->count()
        )
        ->description('Currently active')
        ->color('success'),
        ];
    }
}
