<?php

namespace App\Filament\Resources\PreBookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PreBookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('reference'),
                TextEntry::make('care_plan_id')
                    ->numeric(),
                TextEntry::make('care_type_id')
                    ->numeric(),
                TextEntry::make('booker_details')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('payment_meta')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
