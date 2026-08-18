<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class BookingsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─────────────────────────────────────────────
                // BOOKER DETAILS
                // ─────────────────────────────────────────────
                Section::make('Booker Details')
                    ->schema([
                        TextEntry::make('booker_details.name')
                            ->label('Name')
                            ->placeholder('—'),

                        TextEntry::make('booker_details.phone')
                            ->label('Phone')
                            ->placeholder('—'),

                        TextEntry::make('booker_details.state')
                            ->label('State')
                            ->placeholder('—'),

                        TextEntry::make('booker_details.address')
                            ->label('Address')
                            ->placeholder('—'),

                        TextEntry::make('booker_details.emergency_number')
                            ->label('Emergency Number')
                            ->placeholder('—'),
                    ])
                    ->columns(2),


                // ─────────────────────────────────────────────
                // PATIENT DETAILS
                // ─────────────────────────────────────────────
                Section::make('Patient Details')
                    ->schema([
                        TextEntry::make('patient_details.full_name')
                            ->label('Full Name')
                            ->placeholder('—'),

                        TextEntry::make('patient_details.age')
                            ->label('Age')
                            ->suffix(' years')
                            ->placeholder('—'),

                        TextEntry::make('patient_details.gender')
                            ->label('Gender')
                            ->placeholder('—'),

                        TextEntry::make('patient_details.mobility_level')
                            ->label('Mobility Level')
                            ->badge()
                            ->color(fn ($state) => match (strtolower((string) $state)) {
                                'independent' => 'success',
                                'assisted'    => 'warning',
                                'bedridden'   => 'danger',
                                default       => 'gray',
                            }),

                        TextEntry::make('patient_details.address')
                            ->label('Patient Address')
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make('patient_details.diagnosis')
                            ->label('Diagnosis')
                            ->placeholder('None provided')
                            ->columnSpanFull(),

                        TextEntry::make('patient_details.emergency_contact')
                            ->label('Emergency Contact')
                            ->placeholder('None provided'),
                    ])
                    ->columns(2),


                // ─────────────────────────────────────────────
                // MEDICAL / CARE INFORMATION
                // ─────────────────────────────────────────────
                Section::make('Care Information')
                    ->schema([
                        TextEntry::make('patient_details.medications')
                            ->label('Medications')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return 'None provided';
                                }

                                return collect($state)
                                    ->pluck('medication')
                                    ->filter()
                                    ->implode(', ');
                            })
                            ->columnSpanFull(),

                        TextEntry::make('patient_details.allergies')
                            ->label('Allergies')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return 'None provided';
                                }

                                return collect($state)
                                    ->pluck('allergy')
                                    ->filter()
                                    ->implode(', ');
                            })
                            ->columnSpanFull(),

                        TextEntry::make('patient_details.daily_tasks')
                            ->label('Daily Care Tasks')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return 'None provided';
                                }

                                return collect($state)
                                    ->pluck('task')
                                    ->filter()
                                    ->implode(', ');
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(1),


                // ─────────────────────────────────────────────
                // BOOKING DETAILS
                // ─────────────────────────────────────────────
                Section::make('Booking Details')
                    ->schema([
                        TextEntry::make('reference')
                            ->label('Booking Reference')
                            ->weight('bold'),

                        TextEntry::make('careType.name')
                            ->label('Care Type')
                            ->placeholder('—'),

                        TextEntry::make('carePlan.name')
                            ->label('Care Plan')
                            ->placeholder('—'),

                        TextEntry::make('booking_details.start_date')
                            ->label('Start Date')
                            ->date()
                            ->placeholder('—'),

                        TextEntry::make('booking_details.end_date')
                            ->label('End Date')
                            ->date()
                            ->placeholder('—'),
                    ])
                    ->columns(2),


                // ─────────────────────────────────────────────
                // STATUS
                // ─────────────────────────────────────────────
                Section::make('Booking Status')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'intake_complete' => 'success',
                                'confirmed'       => 'success',
                                'pending_payment' => 'warning',
                                'paid'             => 'success',
                                'cancelled'        => 'danger',
                                default            => 'gray',
                            }),

                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(3),

            ]);
    }
}