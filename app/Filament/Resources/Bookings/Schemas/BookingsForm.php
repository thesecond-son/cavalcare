<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
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
use Carbon\Carbon;
use App\Models\CareType;
use App\Models\CarePlan;

class BookingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                /* ---------------- Booker Details ---------------- */
                Section::make('Booker Details')
                ->description('Primary contact information')
                ->schema([
                    TextInput::make('booker_details.name')
                        ->label('Full Name (Surname First)')
                        ->required(),

                    TextInput::make('booker_details.phone')
                        ->tel()
                        ->required(),

                    Textarea::make('booker_details.address')
                        ->rows(2),

                    Textarea::make('booker_details.state')
                        ->label('State/Province'),

                    TextInput::make('booker_details.emergency_number')
                        ->tel(),
                ])
                ->columns(2),

                /* ---------------- Patient Details ---------------- */
        Section::make('Patient Details')
        ->schema([
            TextInput::make('patient_details.full_name')
                ->label('Full Name of the Patient (Surname First)')
                ->required(),

            TextInput::make('patient_details.age')
                ->numeric(),

            Select::make('patient_details.gender')
                ->options([
                    'Male' => 'Male',
                    'Female' => 'Female',
                ]),

            Textarea::make('patient_details.address')
                ->rows(2),

            Select::make('patient_details.mobility_level')
                ->options([
                    'Inependent' => 'Independent',
                    'Assisted' => 'Needs Assistance',
                    'Bedridden' => 'Bedridden',
                ]),

            Textarea::make('patient_details.diagnosis')
                ->label('Diagnosis / Conditions'),

            Textarea::make('patient_details.daily_tasks')
                ->label('Daily Tasks Required'),

            Repeater::make('patient_details.medications')
                     ->label('Medications')
                     ->schema([
                         Textarea::make('medication')
                             ->label('Medication')
                             ->placeholder('Enter a medication')
                             ->rows(1)
                             ->required(),
                     ])
                     ->minItems(1) // You can set the minimum number of items
                     ->createItemButtonLabel('Add Medication')
                     ->columns(1),

            Textarea::make('patient_details.allergies'),

            TextInput::make('patient_details.emergency_contact')
                ->label('Emergency Contact Number')
                ->tel(),
        ])
        ->columns(2),

         /* ---------------- Booking Details ---------------- */
         Section::make('Booking Details')
         ->schema([
            Select::make('booking_details.care_type_id')
                ->label('Care Type')
                ->options(fn () => CareType::query()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Select::make('booking_details.care_plan_id')
                ->label('Care Plan')
                ->options(fn () => \App\Models\CarePlan::pluck('name', 'id'))
                ->reactive()
                ->searchable()
                ->preload()
                ->required()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::setEndDate($set, $get);
                }),

            Placeholder::make('booking_details.price')
                ->label('Plan Price')
                ->content(function (callable $get) {
                    $planId = $get('booking_details.care_plan_id');
            
                    return $planId
                        ? '₦' . CarePlan::find($planId)?->price
                        : '—';
                }),
            
            DatePicker::make('booking_details.start_date')
                ->label('Start Date')
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    self::setEndDate($set, $get);
                }),

            DatePicker::make('booking_details.end_date')
                ->label('End Date')
                ->disabled()
                ->dehydrated() // still saved to DB
                ->required(),
            

             Textarea::make('booking_details.notes')
                 ->rows(2),
         ])
         ->columns(2),

         /* ---------------- Payment Details ---------------- */
        Section::make('Payment Details')
        ->schema([
            TextInput::make('payment_details.amount_paid')
                ->numeric()
                ->prefix('₦'),

            Select::make('payment_details.payment_method')
                ->options([
                    'transfer' => 'Bank Transfer',
                    'card' => 'Card',
                    'cash' => 'Cash',
                ]),

            DatePicker::make('payment_details.date_paid'),
        ])
        ->columns(3),

        /* ---------------- Status ---------------- */
        Section::make('Booking Status')
            ->schema([
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                ]),
            ]);
    }

    protected static function setEndDate(callable $set, callable $get): void
    {
        $startDate = $get('booking_details.start_date');
        $carePlanId = $get('booking_details.care_plan_id');

        if (! $startDate || ! $carePlanId) {
            $set('booking_details.end_date', null);
            return;
        }

        $carePlan = \App\Models\CarePlan::find($carePlanId);

        if (! $carePlan?->meta['duration_days']) {
            $set('booking_details.end_date', null);
            return;
        }

        $endDate = Carbon::parse($startDate)
            ->addDays($carePlan->meta['duration_days']);

        $set('booking_details.end_date', $endDate->toDateString());
    }

}
