<?php

namespace App\Filament\Customer\Resources\Bookings\Pages;

use App\Filament\Customer\Resources\Bookings\BookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['reference'] = 'CC-' . strtoupper(str()->random(8));
        $data['status'] = 'pending';

        return $data;
    }
}
