<?php

namespace App\Filament\Resources\PreBookings\Schemas;

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

class PreBookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booker Details')
                ->schema([
                    TextInput::make('booker_details.name')->disabled(),
                    TextInput::make('booker_details.email')->disabled(),
                    TextInput::make('booker_details.phone')->disabled(),
                ])
                ->columns(2),

                Section::make('Care Selection')
                ->schema([
                    Select::make('care_plan_id')
                        ->relationship('carePlan', 'name')
                        ->disabled(),
                
                        Select::make('care_type_id')
                        ->relationship('careType', 'name')
                        ->disabled(),
                ])
                ->columns(2),

               Section::make('Payment')
                ->schema([
                    Textarea::make('payment_meta.notes')
                        ->label('Payment Notes'),

                    FileUpload::make('payment_meta.receipt')
                        ->label('Payment Receipt')
                        ->directory('payment-receipts')
                        ->openable()
                        ->downloadable(),
                ]),

                Select::make('status')
                ->options([
                    'pending_payment' => 'Pending Payment',
                    'paid'            => 'Paid',
                    'converted'       => 'Converted',
                ])
                ->disabled(),
            ]);
    }
}
