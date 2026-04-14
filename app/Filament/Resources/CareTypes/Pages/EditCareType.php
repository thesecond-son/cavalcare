<?php

namespace App\Filament\Resources\CareTypes\Pages;

use App\Filament\Resources\CareTypes\CareTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCareType extends EditRecord
{
    protected static string $resource = CareTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
