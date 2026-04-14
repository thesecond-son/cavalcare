<?php

namespace App\Filament\Resources\CareTypes;

use App\Filament\Resources\CareTypes\Pages\CreateCareType;
use App\Filament\Resources\CareTypes\Pages\EditCareType;
use App\Filament\Resources\CareTypes\Pages\ListCareTypes;
use App\Filament\Resources\CareTypes\Pages\ViewCareType;
use App\Filament\Resources\CareTypes\Schemas\CareTypeForm;
use App\Filament\Resources\CareTypes\Schemas\CareTypeInfolist;
use App\Filament\Resources\CareTypes\Tables\CareTypesTable;
use App\Models\CareType;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CareTypeResource extends Resource
{
    protected static ?string $model = CareType::class;

    protected static string | UnitEnum | null $navigationGroup = 'Care Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Heart;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CareTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CareTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CareTypesTable::configure($table);
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
            'index' => ListCareTypes::route('/'),
            'create' => CreateCareType::route('/create'),
            'view' => ViewCareType::route('/{record}'),
            'edit' => EditCareType::route('/{record}/edit'),
        ];
    }
}
