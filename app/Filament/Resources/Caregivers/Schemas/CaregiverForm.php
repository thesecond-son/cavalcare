<?php

namespace App\Filament\Resources\Caregivers\Schemas;

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

class CaregiverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                ->schema([
                    TextInput::make('profile.full_name')->required(),
                    TextInput::make('profile.email')->email(),
                    TextInput::make('profile.phone'),
                    Select::make('profile.gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                        ]),
                    DatePicker::make('profile.date_of_birth'),
                    TextInput::make('profile.city'),
                    TextInput::make('profile.state'),
                    Textarea::make('profile.bio')->columnSpanFull(),
                ])->columns(2)
                ->columnSpanFull(),
                Section::make('Professional Details')
                ->schema([
                    Select::make('professional.caregiver_type')
                        ->options([
                            'nurse' => 'Nurse',
                            'caregiver' => 'Caregiver',
                            'both' => 'Both',
                        ]),
                    TextInput::make('professional.years_of_experience')->numeric(),
                    Textarea::make('professional.qualifications'),
                    TextInput::make('professional.license_number'),
                    DatePicker::make('professional.license_expiry'),
                ])->columns(2)
                ->columnSpanFull(),
                Section::make('Availability & Capacity')
                ->schema([
                    CheckboxList::make('availability.working_days')
                        ->options([
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday',
                        ]),
                    TextInput::make('availability.max_active_bookings')
                        ->numeric()
                        ->default(1),
                    Toggle::make('availability.is_active')
                        ->default(true),
                ])->columns(2)
                ->columnSpanFull(),
                Section::make('Capabilities')
                ->schema([
                    Select::make('careTypes')
                        ->relationship('careTypes', 'name')
                        ->multiple()
                        ->preload(),

                    Select::make('carePlans')
                        ->relationship('carePlans', 'name')
                        ->multiple()
                        ->preload(),
                ]),
            ]);
    }
}
