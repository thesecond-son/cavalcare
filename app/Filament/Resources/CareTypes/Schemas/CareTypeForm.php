<?php

namespace App\Filament\Resources\CareTypes\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\{
    TextInput,
    Textarea,
    Repeater,
    FileUpload
};

class CareTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Core Information ─────────────────────────────────────
                Section::make('Care Type Information')
                    ->description('Basic information about this care service.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Care Type Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->helperText('Leave blank to generate automatically from the care type name.')
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label('Booking Page Description')
                            ->helperText('Short introductory description shown on the booking page.')
                            ->rows(4)
                            ->columnSpanFull(),

                        TextInput::make('meta.icon')
                            ->label('Bootstrap Icon Class')
                            ->placeholder('e.g. bi-heart-pulse')
                            ->helperText('Used on the homepage services card. Find icons at icons.getbootstrap.com.')
                            ->columnSpanFull(),

                        FileUpload::make('meta.image_path')
                            ->label('Service Image')
                            ->image()
                            ->disk('public')
                            ->directory('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ── Services Page Content ─────────────────────────────────
                Section::make('Service Page Content')
                    ->description('Content displayed on the full Services listing page.')
                    ->schema([
                        Textarea::make('meta.more_info')
                            ->label('More Information')
                            ->helperText('A longer paragraph shown in the highlighted information section beneath the description.')
                            ->rows(5)
                            ->columnSpanFull(),

                        Repeater::make('meta.tasks')
                            ->label('What to Expect')
                            ->helperText('Each entry becomes one bullet point in the "What to expect" section.')
                            ->schema([
                                TextInput::make('task')
                                    ->label('Bullet Point')
                                    ->placeholder('e.g. Vital signs monitoring (BP, temperature, pulse)')
                                    ->required(),
                            ])
                            ->addActionLabel('Add Bullet Point')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}