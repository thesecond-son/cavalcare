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

        TextEntry::make('meta.icon')
            ->label('Bootstrap Icon')
            ->placeholder('-'),

        TextEntry::make('meta.image_path')
            ->label('Service Image')
            ->placeholder('-'),

        TextEntry::make('meta.description')
            ->label('Short Description')
            ->placeholder('-')
            ->columnSpanFull(),

        TextEntry::make('meta.more_info')
            ->label('More Information')
            ->placeholder('-')
            ->columnSpanFull(),

        TextEntry::make('meta.tasks')
            ->label('What to Expect')
            ->formatStateUsing(function ($state) {
                if (! is_array($state)) {
                    return '-';
                }

                return collect($state)
                    ->pluck('task')
                    ->filter()
                    ->implode("\n");
            })
            ->listWithLineBreaks()
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
