<?php

namespace App\Filament\Resources\Addons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AddonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Add-On Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Add-On Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->prefix('₦'),

                        Toggle::make('price_from')
                            ->label('Display as "From" Price')
                            ->helperText('Enable this if the displayed price should appear as "from ₦X".')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->required()
                            ->default(true),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}