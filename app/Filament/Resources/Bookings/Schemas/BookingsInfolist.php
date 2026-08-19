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

                // ═══════════════════════════════════════════════
                // BOOKING DETAILS
                // ═══════════════════════════════════════════════

                Section::make('Booking')
                    ->schema([
                        TextEntry::make('reference')
                            ->label('Booking Reference')
                            ->weight('bold')
                            ->copyable(),

                            TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(function ($state) {
                
                                // BookingStatus is a backed enum.
                                $value = $state instanceof \BackedEnum
                                    ? $state->value
                                    : (string) $state;
                
                                return match ($value) {
                                    'intake_complete' => 'Intake Complete',
                                    'confirmed'       => 'Confirmed',
                                    'completed'       => 'Completed',
                                    'cancelled'       => 'Cancelled',
                
                                    default => ucfirst(
                                        str_replace('_', ' ', $value)
                                    ),
                                };
                            })

                            ->color(function ($state) {
                
                                $value = $state instanceof \BackedEnum
                                    ? $state->value
                                    : (string) $state;
                
                                return match ($value) {
                                    'intake_complete',
                                    'confirmed',
                                    'completed' => 'success',
                
                                    'cancelled' => 'danger',
                
                                    default => 'gray',
                                };
                            }),

                            TextEntry::make('created_at')
                                ->label('Created')
                                ->dateTime('D, d M Y · h:i A'),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime('D, d M Y · h:i A'),
                    ])
                    ->columns(3),

                // ═══════════════════════════════════════════════
                // BOOKER DETAILS
                // ═══════════════════════════════════════════════

                Section::make('Booker Details')
                    ->description('Information provided by the person making the booking.')
                    ->icon('heroicon-o-user')
                    ->schema([

                        TextEntry::make('booker_details.name')
                            ->label('Full Name')
                            ->placeholder('—'),

                        TextEntry::make('booker_details.email')
                            ->label('Email')
                            ->placeholder('Not provided')
                            ->copyable(),

                        TextEntry::make('booker_details.phone')
                            ->label('Phone')
                            ->placeholder('—')
                            ->copyable(),

                        TextEntry::make('booker_details.state')
                            ->label('State')
                            ->placeholder('—'),

                        TextEntry::make('booker_details.address')
                            ->label('Address')
                            ->placeholder('—')
                            ->columnSpanFull(),

                        TextEntry::make('booker_details.emergency_number')
                            ->label('Emergency Number')
                            ->placeholder('Not provided')
                            ->copyable(),

                    ])
                    ->columns(2),


                // ═══════════════════════════════════════════════
                // PATIENT DETAILS
                // ═══════════════════════════════════════════════

                Section::make('Patient Details')
                    ->description('Personal and medical information about the person receiving care.')
                    ->icon('heroicon-o-heart')
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
                            ->label('Care Address')
                            ->placeholder('—')
                            ->columnSpanFull(),

                        TextEntry::make('patient_details.diagnosis')
                            ->label('Diagnosis / Condition')
                            ->placeholder('None provided')
                            ->columnSpanFull(),

                        TextEntry::make('patient_details.emergency_contact')
                            ->label('Patient Emergency Contact')
                            ->placeholder('Not provided')
                            ->copyable(),

                    ])
                    ->columns(2),


               // ═══════════════════════════════════════════════
// MEDICATIONS
// ═══════════════════════════════════════════════

Section::make('Medications')
    ->description('Medications reported for the patient.')
    ->icon('heroicon-o-beaker')
    ->schema([

        TextEntry::make('medications_display')
            ->label('Medications')
            ->state(function ($record) {

                $medications = data_get(
                    $record->patient_details ?? [],
                    'medications',
                    []
                );

                if (is_string($medications)) {
                    $medications = json_decode($medications, true) ?? [];
                }

                if (!is_array($medications)) {
                    return 'No medications provided.';
                }

                $medications = collect($medications)
                    ->map(function ($item) {
                        return is_array($item)
                            ? ($item['medication'] ?? null)
                            : $item;
                    })
                    ->filter(fn ($item) =>
                        filled($item) &&
                        strtolower(trim((string) $item)) !== 'none'
                    )
                    ->values();

                if ($medications->isEmpty()) {
                    return 'No medications provided.';
                }

                return $medications
                    ->map(fn ($medication, $index) =>
                        '<strong>' . ($index + 1) . '.</strong> ' . e($medication)
                    )
                    ->implode('<br>');

            })
            ->html()
            ->columnSpanFull(),

    ])
    ->collapsible(),

               // ═══════════════════════════════════════════════
// ALLERGIES
// ═══════════════════════════════════════════════

Section::make('Allergies')
->description('Known allergies reported for the patient.')
->icon('heroicon-o-exclamation-triangle')
->schema([

    TextEntry::make('allergies_display')
        ->label('Allergies')
        ->state(function ($record) {

            $allergies = data_get(
                $record->patient_details ?? [],
                'allergies',
                []
            );

            if (is_string($allergies)) {
                $allergies = json_decode($allergies, true) ?? [];
            }

            if (!is_array($allergies)) {
                return 'No allergies provided.';
            }

            $allergies = collect($allergies)
                ->map(function ($item) {
                    return is_array($item)
                        ? ($item['allergy'] ?? null)
                        : $item;
                })
                ->filter(fn ($item) =>
                    filled($item) &&
                    strtolower(trim((string) $item)) !== 'none'
                )
                ->values();

            if ($allergies->isEmpty()) {
                return 'No allergies provided.';
            }

            return $allergies
                ->map(fn ($allergy, $index) =>
                    '<strong>' . ($index + 1) . '.</strong> ' . e($allergy)
                )
                ->implode('<br>');

        })
        ->html()
        ->columnSpanFull(),

])
->collapsible(),


                // ═══════════════════════════════════════════════
// DAILY CARE TASKS
// ═══════════════════════════════════════════════

Section::make('Daily Care Tasks')
->description('Daily activities and care tasks requested for the patient.')
->icon('heroicon-o-clipboard-document-list')
->schema([

    TextEntry::make('daily_tasks_display')
        ->label('Daily Care Tasks')
        ->state(function ($record) {

            $tasks = data_get(
                $record->patient_details ?? [],
                'daily_tasks',
                []
            );

            if (is_string($tasks)) {
                $tasks = json_decode($tasks, true) ?? [];
            }

            if (!is_array($tasks)) {
                return 'No daily care tasks provided.';
            }

            $tasks = collect($tasks)
                ->map(function ($item) {
                    return is_array($item)
                        ? ($item['task'] ?? null)
                        : $item;
                })
                ->filter(fn ($item) =>
                    filled($item) &&
                    strtolower(trim((string) $item)) !== 'none'
                )
                ->values();

            if ($tasks->isEmpty()) {
                return 'No daily care tasks provided.';
            }

            return $tasks
                ->map(fn ($task, $index) =>
                    '<strong>' . ($index + 1) . '.</strong> ' . e($task)
                )
                ->implode('<br>');

        })
        ->html()
        ->columnSpanFull(),

])
->collapsible(),

                // ═══════════════════════════════════════════════
                // BOOKING DETAILS
                // ═══════════════════════════════════════════════

                Section::make('Booking Details')
                    ->description('Care service and scheduling information.')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([

                        TextEntry::make('reference')
                            ->label('Booking Reference')
                            ->weight('bold')
                            ->copyable(),

                        TextEntry::make('careType.name')
                            ->label('Care Type')
                            ->placeholder('—'),

                        TextEntry::make('carePlan.name')
                            ->label('Care Plan')
                            ->placeholder('—'),

                        TextEntry::make('booking_details.start_date')
                            ->label('Care Start Date')
                            ->date('D, d M Y')
                            ->placeholder('—'),

                        TextEntry::make('booking_details.end_date')
                            ->label('Care End Date')
                            ->date('D, d M Y')
                            ->placeholder('Not specified'),

                    ])
                    ->columns(2),

            ]);
    }
}