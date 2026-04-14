<?php

namespace App\Filament\Resources\PreBookings\Pages;

use App\Filament\Resources\PreBookings\PreBookingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPreBooking extends ViewRecord
{
    protected static string $resource = PreBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
