<?php

namespace App\Filament\Resources\CarePlans\Schemas;

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
use Filament\Schemas\Schema;

class CarePlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Care Plan Details')
                ->schema([
                    TextInput::make('name')
                        ->required(),

                    TextInput::make('price')
                        ->numeric()
                        ->prefix('₦')
                        ->required(),

                    TextInput::make('meta.duration_days')
                        ->label('Duration (Days)')
                        ->numeric()
                        ->required(),

                    TextInput::make('meta.daily_hours')
                        ->label('Hours Per Day')
                        ->numeric(),

                    TextInput::make('meta.max_caregivers')
                        ->label('Max Caregivers')
                        ->numeric()
                        ->default(1),

                    Select::make('meta.recommended_for')
                        ->label('Recommended For Care Types')
                        ->multiple()
                        ->options(fn () =>
                            \App\Models\CareType::pluck('name', 'id')->toArray()
                        ),

                    Toggle::make('meta.is_long_term')
                        ->label('Long-Term Plan')
                        ->default(false),
                ])
                ->columns(2),
            ]);
    }
}
