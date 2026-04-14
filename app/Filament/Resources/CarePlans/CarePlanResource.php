<?php

namespace App\Filament\Resources\CarePlans;

use App\Filament\Resources\CarePlans\Pages\CreateCarePlan;
use App\Filament\Resources\CarePlans\Pages\EditCarePlan;
use App\Filament\Resources\CarePlans\Pages\ListCarePlans;
use App\Filament\Resources\CarePlans\Pages\ViewCarePlan;
use App\Filament\Resources\CarePlans\Schemas\CarePlanForm;
use App\Filament\Resources\CarePlans\Schemas\CarePlanInfolist;
use App\Filament\Resources\CarePlans\Tables\CarePlansTable;
use App\Models\CarePlan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CarePlanResource extends Resource
{
    protected static ?string $model = CarePlan::class;
    
    protected static string | UnitEnum | null $navigationGroup = 'Care Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocument;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CarePlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CarePlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarePlansTable::configure($table);
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
            'index' => ListCarePlans::route('/'),
            'create' => CreateCarePlan::route('/create'),
            'view' => ViewCarePlan::route('/{record}'),
            'edit' => EditCarePlan::route('/{record}/edit'),
        ];
    }
}
