<?php

namespace App\Filament\Resources\CareTypes\Schemas;

use Dom\Text;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CareTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('meta.description')
                    ->label('Description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta.more_info')
                    ->label('More Information')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta.tasks')
                    ->label('Care Tasks')
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
