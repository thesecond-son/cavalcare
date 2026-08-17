<?php

namespace App\Filament\Resources\CarePlans\Schemas;

use Filament\Schemas\Components\Section;
use App\Models\CareType;
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
                    ->description('Define the care plan, its pricing, and the type of care it belongs to.')
                    ->schema([

                        Select::make('care_type_id')
                            ->label('Care Type')
                            ->relationship('careType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Plan Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->helperText('Leave blank to generate automatically from the plan name.')
                            ->unique(ignoreRecord: true),

                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('₦')
                            ->required(),

                        Select::make('billing_cycle')
                            ->label('Billing Cycle')
                            ->options([
                                'per_day'   => 'Per Day',
                                'per_night' => 'Per Night',
                                'per_24h'   => 'Per 24 Hours',
                                'per_week'  => 'Per Week',
                                'per_month' => 'Per Month',
                                'flat'      => 'One-Time / Flat Fee',
                            ])
                            ->required()
                            ->native(false),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull(),
                ])
                ->columns(2),

                Section::make('Plan Configuration')
                    ->description('Additional settings stored in the plan metadata.')
                    ->schema([

                        TextInput::make('meta.duration_days')
                            ->label('Duration (Days)')
                            ->numeric()
                            ->required(),

                        TextInput::make('meta.daily_hours')
                            ->label('Hours Per Day')
                            ->numeric(),

                        TextInput::make('meta.max_caregivers')
                            ->label('Maximum Caregivers')
                            ->numeric()
                            ->default(1),

                        Select::make('meta.recommended_for')
                            ->label('Recommended For Care Types')
                            ->multiple()
                            ->options(fn () => CareType::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload(),

                            Toggle::make('meta.is_long_term')
                            ->label('Long-Term Plan')
                            ->default(false),

                        Textarea::make('meta.features')
                            ->label('Features')
                            ->helperText('Enter one feature per line.')
                            ->rows(5)
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode("\n", $state);
                                }

                                return $state;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (blank($state)) {
                                    return [];
                                }

                                return array_values(
                                    array_filter(
                                        array_map('trim', preg_split('/\r\n|\r|\n/', $state))
                                    )
                                );
                            })
                            ->columnSpanFull(),

                    ])
                    ->columns(2),
            ]);
    }
}
