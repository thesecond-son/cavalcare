<?php

namespace App\Filament\Resources\Caregivers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CaregiverInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('profile')
                    ->columnSpanFull(),
                TextEntry::make('professional')
                    ->columnSpanFull(),
                TextEntry::make('availability')
                    ->columnSpanFull(),
                TextEntry::make('performance')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_verified')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
