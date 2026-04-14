<?php

namespace App\Filament\Resources\PreBookings;

use App\Filament\Resources\PreBookings\Pages\CreatePreBooking;
use App\Filament\Resources\PreBookings\Pages\EditPreBooking;
use App\Filament\Resources\PreBookings\Pages\ListPreBookings;
use App\Filament\Resources\PreBookings\Pages\ViewPreBooking;
use App\Filament\Resources\PreBookings\Schemas\PreBookingForm;
use App\Filament\Resources\PreBookings\Schemas\PreBookingInfolist;
use App\Filament\Resources\PreBookings\Tables\PreBookingsTable;
use App\Models\PreBooking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PreBookingResource extends Resource
{
    protected static ?string $model = PreBooking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PreBookingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PreBookingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreBookingsTable::configure($table);
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
            'index' => ListPreBookings::route('/'),
            'create' => CreatePreBooking::route('/create'),
            'view' => ViewPreBooking::route('/{record}'),
            'edit' => EditPreBooking::route('/{record}/edit'),
        ];
    }
}
