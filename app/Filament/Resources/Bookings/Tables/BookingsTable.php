<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Services\Booking\PaymentConfirmationService;
use App\Services\Booking\CustomerAccountCreator;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('booker_details.name')
                ->label('Booker')
                ->searchable(),

            TextColumn::make('patient_details.full_name')
                ->label('Patient')
                ->searchable(),

            TextColumn::make('booking_details.care_type')
                ->label('Care Type')
                ->badge(),

            TextColumn::make('booking_details.care_plan')
                ->label('Plan')
                ->badge(),

            TextColumn::make('payment_details.amount_paid')
                ->label('Amount Paid')
                ->money('NGN'),

            BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'confirmed',
                    'primary' => 'completed',
                    'danger' => 'cancelled',
                ]),

            TextColumn::make('careType.name')
                ->label('Care Type')
                ->badge(),

            TextColumn::make('carePlan.name')
                ->label('Care Plan')
                ->badge()
                ->color('info'),

            TextColumn::make('created_at')
                ->dateTime()
                ->label('Booked On'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('confirmPayment')
                    ->label('Confirm Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        app(PaymentConfirmationService::class)
                            ->confirm($record, [
                                'method' => 'manual',
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
