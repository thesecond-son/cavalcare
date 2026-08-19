<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Models\Booking;
use App\Services\Booking\PaymentConfirmationService;
use App\Services\Booking\CustomerAccountCreator;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\BookingStatus;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ─────────────────────────────────────────────
                // BOOKING REFERENCE
                // ─────────────────────────────────────────────
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),


                // ─────────────────────────────────────────────
                // BOOKER
                // ─────────────────────────────────────────────
                TextColumn::make('booker_details.name')
                    ->label('Booker')
                    ->searchable()
                    ->sortable(),


                // ─────────────────────────────────────────────
                // PATIENT
                // ─────────────────────────────────────────────
                TextColumn::make('patient_details.full_name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),


                // ─────────────────────────────────────────────
                // CARE TYPE
                // ─────────────────────────────────────────────
                TextColumn::make('careType.name')
                    ->label('Care Type')
                    ->badge()
                    ->searchable(),


                // ─────────────────────────────────────────────
                // CARE PLAN
                // ─────────────────────────────────────────────
                TextColumn::make('carePlan.name')
                    ->label('Care Plan')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                
                TextColumn::make('addons')
                    ->label('Add-ons')
                    ->state(function ($record) {
                
                        $addOns = $record->preBooking?->addOns;
                
                        if (! $addOns || $addOns->isEmpty()) {
                            return 'None';
                        }
                
                        return $addOns
                            ->map(fn ($addOn) => $addOn->name)
                            ->implode(', ');
                    })
                    ->wrap(),

                
                TextColumn::make('addon_total')
                    ->label('Add-ons')
                    ->state(function ($record) {
                
                        $addOns = $record->preBooking?->addOns;
                
                        if (! $addOns || $addOns->isEmpty()) {
                            return '₦0.00';
                        }
                
                        $total = $addOns->sum(
                            fn ($addOn) => $addOn->pivot->price_at_booking
                        );
                
                        return '₦' . number_format($total, 2);
                    }),


                // ─────────────────────────────────────────────
                // START DATE
                // ─────────────────────────────────────────────
                TextColumn::make('booking_details.start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),


                // ─────────────────────────────────────────────
                // STATUS
                // ─────────────────────────────────────────────
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {

                        BookingStatus::INTAKE_COMPLETE => 'warning',
                        BookingStatus::CONFIRMED       => 'success',
                        BookingStatus::COMPLETED       => 'gray',
                        BookingStatus::CANCELLED       => 'failed',

                        default           => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {

                        'pending'         => 'Pending',
                        'pending_payment' => 'Pending Payment',
                        'paid'            => 'Paid',
                        BookingStatus::CONFIRMED        => 'Confirmed',
                        BookingStatus::INTAKE_COMPLETE => 'Intake Complete',
                        BookingStatus::COMPLETED       => 'Completed',
                        BookingStatus::CANCELLED       => 'Cancelled',

                        default           => ucfirst(str_replace('_', ' ', $state)),
                    }),

                
                TextColumn::make('preBooking.amount_paid')
                    ->label('Amount Paid')
                    ->formatStateUsing(fn ($state) =>
                        $state !== null
                            ? '₦' . number_format($state / 100, 2)
                            : '—'
                    )
                    ->sortable(),

                
                // ─────────────────────────────────────────────
                // BOOKED ON
                // ─────────────────────────────────────────────
                TextColumn::make('created_at')
                    ->label('Booked On')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                TrashedFilter::make(),
            ])

            ->recordActions([

                // View booking
                ViewAction::make(),

                // Edit booking
                EditAction::make(),

                // ─────────────────────────────────────────
                // MANUALLY CONFIRM PAYMENT
                // ─────────────────────────────────────────
                Action::make('confirmPayment')
                    ->label('Confirm Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')

                    ->visible(fn (Booking $record) =>
                        $record->status === 'pending'
                    )

                    ->requiresConfirmation()

                    ->modalHeading('Confirm Payment')

                    ->modalDescription(
                        'Are you sure you want to manually confirm payment for this booking?'
                    )

                    ->action(function (Booking $record) {

                        app(PaymentConfirmationService::class)
                            ->confirm($record, [
                                'method'       => 'manual',
                                'confirmed_by' => auth()->id(),
                            ]);

                        app(CustomerAccountCreator::class)
                            ->createFromBooking($record);
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}