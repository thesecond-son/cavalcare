<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Models\CarePlan;
use App\Models\CareType;
use Carbon\Carbon;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =====================================================
                // BOOKER DETAILS
                // =====================================================
                Section::make('Booker Details')
                    ->description('Primary contact information')
                    ->schema([
                        TextInput::make('booker_details.name')
                            ->label('Full Name')
                            ->required(),

                        TextInput::make('booker_details.phone')
                            ->label('Phone')
                            ->tel()
                            ->required(),

                        TextInput::make('booker_details.state')
                            ->label('State')
                            ->required(),

                        Textarea::make('booker_details.address')
                            ->label('Address')
                            ->rows(2)
                            ->required(),

                        TextInput::make('booker_details.emergency_number')
                            ->label('Emergency Number')
                            ->tel(),
                    ])
                    ->columns(2),


                // =====================================================
                // PATIENT DETAILS
                // =====================================================
                Section::make('Patient Details')
                    ->description('Information about the person receiving care')
                    ->schema([
                        TextInput::make('patient_details.full_name')
                            ->label('Full Name')
                            ->required(),

                        TextInput::make('patient_details.age')
                            ->label('Age')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(120)
                            ->required(),

                        Select::make('patient_details.gender')
                            ->label('Gender')
                            ->options([
                                'Male'   => 'Male',
                                'Female' => 'Female',
                            ])
                            ->required(),

                        Select::make('patient_details.mobility_level')
                            ->label('Mobility Level')
                            ->options([
                                'Independent' => 'Independent',
                                'Assisted'    => 'Needs Assistance',
                                'Bedridden'   => 'Bedridden',
                            ])
                            ->required(),

                        Textarea::make('patient_details.address')
                            ->label('Patient Address')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('patient_details.diagnosis')
                            ->label('Diagnosis / Conditions')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('patient_details.emergency_contact')
                            ->label('Emergency Contact')
                            ->tel(),

                    ])
                    ->columns(2),


                // =====================================================
                // MEDICAL / CARE INFORMATION
                // =====================================================
                Section::make('Medical & Care Information')
                    ->schema([

                        Repeater::make('patient_details.medications')
                            ->label('Medications')
                            ->schema([
                                TextInput::make('medication')
                                    ->label('Medication')
                                    ->required(),
                            ])
                            ->addActionLabel('Add Medication')
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Repeater::make('patient_details.allergies')
                            ->label('Allergies')
                            ->schema([
                                TextInput::make('allergy')
                                    ->label('Allergy')
                                    ->required(),
                            ])
                            ->addActionLabel('Add Allergy')
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Repeater::make('patient_details.daily_tasks')
                            ->label('Daily Care Tasks')
                            ->schema([
                                TextInput::make('task')
                                    ->label('Task')
                                    ->required(),
                            ])
                            ->addActionLabel('Add Care Task')
                            ->defaultItems(0)
                            ->columnSpanFull(),

                    ])
                    ->columns(1),


                // =====================================================
                // BOOKING DETAILS
                // =====================================================
                Section::make('Booking Details')
                    ->description('Care plan and scheduling information')
                    ->schema([

                        Select::make('booking_details.care_type_id')
                            ->label('Care Type')
                            ->options(
                                fn () => CareType::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Select::make('booking_details.care_plan_id')
                            ->label('Care Plan')
                            ->options(function (callable $get) {

                                $careTypeId = $get('booking_details.care_type_id');

                                if (! $careTypeId) {
                                    return [];
                                }

                                return CarePlan::query()
                                    ->where('care_type_id', $careTypeId)
                                    ->orderBy('price')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                self::setEndDate($set, $get);
                            }),

                        Placeholder::make('plan_price')
                            ->label('Plan Price')
                            ->content(function (callable $get) {

                                $planId = $get('booking_details.care_plan_id');

                                if (! $planId) {
                                    return '—';
                                }

                                $plan = CarePlan::find($planId);

                                return $plan
                                    ? '₦' . number_format($plan->price, 2)
                                    : '—';
                            }),

                        DatePicker::make('booking_details.start_date')
                            ->label('Start Date')
                            ->required()
                            ->minDate(now()->startOfDay())
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                self::setEndDate($set, $get);
                            }),

                        DatePicker::make('booking_details.end_date')
                            ->label('End Date')
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                    ])
                    ->columns(2),


                // =====================================================
                // PAYMENT INFORMATION
                // =====================================================
                Section::make('Payment Information')
                    ->description('Payment information from the original PreBooking')
                    ->schema([

                        Placeholder::make('payment_amount')
                            ->label('Amount Paid')
                            ->content(function ($record) {

                                $amount = $record?->preBooking?->amount_paid;

                                return $amount !== null
                                    ? '₦' . number_format($amount / 100, 2)
                                    : 'Not available';
                            }),

                        Placeholder::make('payment_reference')
                            ->label('Paystack Reference')
                            ->content(function ($record) {

                                return $record?->preBooking?->paystack_reference
                                    ?? 'Not available';
                            }),

                        Placeholder::make('payment_status')
                            ->label('Payment Status')
                            ->content(function ($record) {

                                return $record?->preBooking?->status
                                    ?? 'Not available';
                            }),

                    ])
                    ->columns(3),


                // =====================================================
                // ADD-ONS
                // =====================================================
                Section::make('Add-ons')
                    ->description('Additional services selected during booking')
                    ->schema([

                        Placeholder::make('selected_addons')
                            ->label('Selected Add-ons')
                            ->content(function ($record) {

                                $addOns = $record?->preBooking?->addOns;

                                if (! $addOns || $addOns->isEmpty()) {
                                    return 'No add-ons selected';
                                }

                                return $addOns
                                    ->map(function ($addOn) {

                                        $price = $addOn->pivot?->price_at_booking
                                            ?? $addOn->price;

                                        return $addOn->name
                                            . ' — ₦'
                                            . number_format($price, 2);
                                    })
                                    ->implode("\n");
                            })
                            ->columnSpanFull(),

                    ]),


                // =====================================================
                // STATUS
                // =====================================================
                Section::make('Booking Status')
                    ->schema([

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'confirmed'       => 'Confirmed',
                                'intake_complete' => 'Intake Complete',
                                'completed'       => 'Completed',
                                'cancelled'       => 'Cancelled',
                            ])
                            ->required(),

                    ]),
            ]);
    }


    protected static function setEndDate(
        callable $set,
        callable $get
    ): void {

        $startDate = $get('booking_details.start_date');
        $carePlanId = $get('booking_details.care_plan_id');

        if (! $startDate || ! $carePlanId) {
            $set('booking_details.end_date', null);
            return;
        }

        $carePlan = CarePlan::find($carePlanId);

        $durationDays = $carePlan?->meta['duration_days'] ?? null;

        if (! $durationDays) {
            $set('booking_details.end_date', null);
            return;
        }

        $endDate = Carbon::parse($startDate)
            ->addDays($durationDays);

        $set(
            'booking_details.end_date',
            $endDate->toDateString()
        );
    }
}