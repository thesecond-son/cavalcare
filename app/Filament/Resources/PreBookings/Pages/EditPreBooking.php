<?php

namespace App\Filament\Resources\PreBookings\Pages;

use App\Filament\Resources\PreBookings\PreBookingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPreBooking extends EditRecord
{
    protected static string $resource = PreBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
