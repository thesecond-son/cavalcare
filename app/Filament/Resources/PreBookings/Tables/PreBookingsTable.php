<?php

namespace App\Filament\Resources\PreBookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use App\Services\Booking\PreBookingConfirmationService;
use App\Models\PreBooking;
use App\PreBookingStatus;


class PreBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                
                TextColumn::make('booker_details.name')
                    ->label('Customer'),

                TextColumn::make('carePlan.name')
                    ->label('Plan'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        PreBookingStatus::PENDING_PAYMENT => 'warning',
                        PreBookingStatus::PAID            => 'success',
                        PreBookingStatus::CONVERTED       => 'gray',
                        default                            => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('confirmPayment')
                    ->label('Confirm Payment')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PreBooking $record) => $record->status === PreBookingStatus::PENDING_PAYMENT)
                    ->action(function (PreBooking $record) {
                        app(PreBookingConfirmationService::class)
                            ->confirm($record);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
