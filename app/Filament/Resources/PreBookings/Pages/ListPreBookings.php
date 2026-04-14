<?php

namespace App\Filament\Resources\PreBookings\Pages;

use App\Filament\Resources\PreBookings\PreBookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPreBookings extends ListRecords
{
    protected static string $resource = PreBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
