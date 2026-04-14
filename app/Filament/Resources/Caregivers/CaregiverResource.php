<?php

namespace App\Filament\Resources\Caregivers;

use App\Filament\Resources\Caregivers\Pages\CreateCaregiver;
use App\Filament\Resources\Caregivers\Pages\EditCaregiver;
use App\Filament\Resources\Caregivers\Pages\ListCaregivers;
use App\Filament\Resources\Caregivers\Pages\ViewCaregiver;
use App\Filament\Resources\Caregivers\Schemas\CaregiverForm;
use App\Filament\Resources\Caregivers\Schemas\CaregiverInfolist;
use App\Filament\Resources\Caregivers\Tables\CaregiversTable;
use App\Models\Caregiver;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CaregiverResource extends Resource
{
    protected static ?string $model = Caregiver::class;

    protected static string | UnitEnum | null $navigationGroup = 'Care Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CaregiverForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CaregiverInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CaregiversTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaregivers::route('/'),
            'create' => CreateCaregiver::route('/create'),
            'view' => ViewCaregiver::route('/{record}'),
            'edit' => EditCaregiver::route('/{record}/edit'),
        ];
    }
}
