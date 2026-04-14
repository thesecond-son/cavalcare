<?php

namespace App\Filament\Resources\CareTypes\Pages;

use App\Filament\Resources\CareTypes\CareTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCareTypes extends ListRecords
{
    protected static string $resource = CareTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
