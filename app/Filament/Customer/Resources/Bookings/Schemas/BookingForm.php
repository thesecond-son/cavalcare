<?php

namespace App\Filament\Customer\Resources\Bookings\Schemas;

use Filament\Forms\Components\{
    TextInput,
    Textarea,
    Select,
    DatePicker,
    Hidden,
    Repeater,
    Placeholder
};
use Filament\Schemas\Components\Section;
use App\Models\CarePlan;
use App\Models\CareType;
use Carbon\Carbon;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            
                /* ---------------- Booker ---------------- */
            Section::make('Your Details')
            ->schema([
                TextInput::make('booker_details.name')
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

             /* ---------------- Patient ---------------- */
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
                     ->label('Medical Conditions'),

                Repeater::make('patient_details.daily_tasks')
                     ->label('Daily Tasks Required')
                     ->schema([
                         Textarea::make('task')
                             ->label('Task')
                             ->placeholder('Enter a daily task')
                             ->rows(1)
                             ->required(),
                     ])
                     ->minItems(1)
                     ->createItemButtonLabel('Add Task')
                    ->columns(1),

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
                     ->columns(1), // You can modify the number of columns for the repeater
                 

                Repeater::make('patient_details.allergies')
                    ->label('Allergies')
                    ->schema([
                        Textarea::make('allergy')
                            ->label('Allergy')
                            ->placeholder('Enter an allergy')
                            ->rows(1)
                            ->required(),
                    ])
                    ->minItems(0)
                    ->createItemButtonLabel('Add Allergy')
                    ->columns(1),

                TextInput::make('patient_details.emergency_contact')
                    ->label('Emergency Contact Number')
                    ->tel(),
             ])
             ->columns(2),

             /* ---------------- Care ---------------- */
             Section::make('Care Plan')
                ->schema([
                    Select::make('care_type_id')
                        ->label('Choose Care Type')
                        ->options(CareType::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('care_plan_id')
                        ->label('Choose Care Plan')
                        ->options(CarePlan::pluck('name', 'id'))
                        ->reactive()
                        ->required(),

                    Placeholder::make('booking_details.price')
                        ->label('Plan Price')
                        ->content(function (callable $get) {
                            $planId = $get('booking_details.care_plan_id');
                    
                            return $planId
                                ? '₦' . CarePlan::find($planId)?->price
                                : '—';
                        }),

                    DatePicker::make('booking_details.start_date')
                        ->label('Select the date to start the plan')
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(fn ($state, $set, $get) =>
                            self::setEndDate($set, $get)
                        ),

                    DatePicker::make('booking_details.end_date')
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(2),

                 /* Hidden system fields */
                Hidden::make('status')->default('pending'),
            ]);
    }

    protected static function setEndDate(callable $set, callable $get): void
    {
        $planId = $get('care_plan_id');
        $start  = $get('booking_details.start_date');

        if (! $planId || ! $start) {
            $set('booking_details.end_date', null);
            return;
        }

        $plan = CarePlan::find($planId);

        if (! isset($plan->meta['duration_days'])) return;

        $set(
            'booking_details.end_date',
            Carbon::parse($start)
                ->addDays($plan->meta['duration_days'])
                ->toDateString()
        );
    }
}
