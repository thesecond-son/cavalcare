<?php

namespace App\Filament\Customer\Resources\Bookings\Schemas;

use App\Models\Booking;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booker Details')
                ->components([
                TextEntry::make('booker_details.name'),
                TextEntry::make('booker_details.phone'),
                TextEntry::make('booker_details.address'),
                TextEntry::make('booker_details.emergency_number'),
                ])
                ->columns(2),

                Section::make('Patient Details')
                ->schema([
                    TextEntry::make('patient_details.full_name'),
                    TextEntry::make('patient_details.age'),
                    TextEntry::make('patient_details.gender'),
                    TextEntry::make('patient_details.mobility_level')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'independent' => 'success',
                            'assisted' => 'warning',
                            'bedridden' => 'danger',
                            default => 'gray',
                        }),
                    TextEntry::make('patient_details.diagnosis'),
                    TextEntry::make('patient_details.daily_tasks'),
                    TextEntry::make('patient_details.medications'),
                    TextEntry::make('patient_details.allergies'),
                    TextEntry::make('patient_details.emergency_contact'),
                ])
                ->columns(2),


                Section::make('Booking Details')
                ->schema([
                    TextEntry::make('booking_details.care_type'),
                    TextEntry::make('booking_details.care_plan'),
                    TextEntry::make('booking_details.start_date')->date(),
                    TextEntry::make('booking_details.notes'),
                ])
                ->columns(2),

                Section::make('Status')
                ->schema([
                    TextEntry::make('status')->badge(),
                ]),

            ]);
    }
}
