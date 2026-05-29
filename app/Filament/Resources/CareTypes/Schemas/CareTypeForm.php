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
use Filament\Infolists\Components\ImageEntry;

class CareTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Core info ────────────────────────────────────────────
                Section::make('Care Type Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
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
                        ->directory('care-types')
                        ->visibility('public')
                        ->imageEditor()
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

                // ── Content shown on the Services page ───────────────────
                Section::make('Service Page Content')
                ->description('This content appears on the full Services listing page.')
                ->schema([
                    Textarea::make('meta.description')
                        ->label('Short Description')
                        ->helperText('One or two sentences shown at the top of the service entry and on the homepage card.')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('meta.more_info')
                        ->label('More Information')
                        ->helperText('A longer paragraph shown in the highlighted callout block beneath the description.')
                        ->rows(4)
                        ->columnSpanFull(),

                    // "What to expect" bullet list (shown as the bullet list under the service)
                    Repeater::make('meta.tasks')
                        ->label('What to Expect (bullet list)')
                        ->helperText('Each entry becomes one bullet point in the "What to expect" section on the Services page.')
                        ->schema([
                            TextInput::make('task')
                                ->label('Bullet point')
                                ->placeholder('e.g. Vital signs monitoring (BP, temperature, pulse)')
                                ->required(),
                        ])
                        ->addActionLabel('Add bullet point')
                        ->reorderable()
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->columnSpanFull(),
            ]);
    }
}