<?php

namespace App\Filament\Resources\CareTypes\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\{
    TextInput,
    Textarea,
    Repeater,
    Select,
    DatePicker,
    FileUpload, 
    CheckboxList,
    Toggle
};

class CareTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Care Type Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('meta.description')
                        ->label('Description')
                        ->columnSpanFull(),

                    Repeater::make('meta.tasks')
                        ->label('Care Tasks')
                        ->schema([
                    TextInput::make('task')
                                ->required(),
                        ])
                        ->columnSpanFull(),

                    TextInput::make('meta.icon')
                        ->label('Icon / Asset Name')
                        ->helperText('Optional icon reference')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
                // Textarea::make('meta')
                //     ->default(null)
                //     ->columnSpanFull(),
            ]);
    }
}
