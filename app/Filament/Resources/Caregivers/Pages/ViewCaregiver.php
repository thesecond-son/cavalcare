<?php

namespace App\Filament\Resources\Caregivers\Pages;

use App\Filament\Resources\Caregivers\CaregiverResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCaregiver extends ViewRecord
{
    protected static string $resource = CaregiverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
