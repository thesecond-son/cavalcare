<?php

namespace App\Filament\Resources\CareTypes\Pages;

use App\Filament\Resources\CareTypes\CareTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCareType extends ViewRecord
{
    protected static string $resource = CareTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
