<?php

namespace App\Filament\Resources\CarePlans\Pages;

use App\Filament\Resources\CarePlans\CarePlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCarePlan extends ViewRecord
{
    protected static string $resource = CarePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
