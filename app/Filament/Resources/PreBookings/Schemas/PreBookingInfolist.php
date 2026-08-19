<?php

namespace App\Filament\Resources\PreBookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PreBookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─────────────────────────────────────────────
                // BOOKING INFORMATION
                // ─────────────────────────────────────────────
                Section::make('Booking Information')
                    ->schema([
                        TextEntry::make('reference')
                            ->label('Pre-Booking Reference')
                            ->weight('bold')
                            ->copyable(),

                        TextEntry::make('carePlan.name')
                            ->label('Care Plan')
                            ->placeholder('-'),

                        TextEntry::make('careType.name')
                            ->label('Care Type')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'pending' => 'warning',
                                'pending_payment' => 'warning',
                                'paid' => 'success',
                                'converted' => 'gray',
                                'failed' => 'danger',
                                'intake_complete' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('booking.reference')
                            ->label('Final Booking Reference')
                            ->placeholder('Not yet converted')
                            ->copyable(),

                        TextEntry::make('token')
                            ->label('Intake Token')
                            ->placeholder('-')
                            ->copyable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ─────────────────────────────────────────────
                // CUSTOMER INFORMATION
                // ─────────────────────────────────────────────
                Section::make('Customer Information')
                    ->schema([
                        TextEntry::make('booker_details.name')
                            ->label('Name')
                            ->placeholder('-'),

                        TextEntry::make('booker_details.email')
                            ->label('Email')
                            ->placeholder('-'),

                        TextEntry::make('booker_details.phone')
                            ->label('Phone')
                            ->placeholder('-'),

                        TextEntry::make('booker_details.state')
                            ->label('State')
                            ->placeholder('-'),

                        TextEntry::make('booker_details.address')
                            ->label('Address')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('booker_details.emergency_number')
                            ->label('Emergency Number')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                // ─────────────────────────────────────────────
                // PAYMENT INFORMATION
                // ─────────────────────────────────────────────
                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('paystack_reference')
                            ->label('Paystack Reference')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('amount_paid')
                            ->label('Amount Paid')
                            ->state(fn ($record) =>
                                '₦' . number_format(((float) $record->amount_paid) / 100, 2)
                            )
                            ->weight('bold'),

                            TextEntry::make('payment_meta.transaction_id')
                            ->label('Transaction ID')
                            ->placeholder('—'),
                        
                        TextEntry::make('payment_meta.status')
                            ->label('Payment Status')
                            ->badge(),
                        
                        TextEntry::make('payment_meta.currency')
                            ->label('Currency')
                            ->placeholder('—'),
                        
                        TextEntry::make('payment_meta.channel')
                            ->label('Payment Channel')
                            ->placeholder('—'),
                        
                        TextEntry::make('payment_meta.fees')
                            ->label('Paystack Fees')
                            ->money('NGN')
                            ->placeholder('—'),
                        
                        TextEntry::make('payment_meta.paid_at')
                            ->label('Paid At')
                            ->dateTime()
                            ->placeholder('—'),
                        
                        TextEntry::make('payment_meta.customer.email')
                            ->label('Customer Email')
                            ->copyable()
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                // ─────────────────────────────────────────────
// SELECTED ADD-ONS
// ─────────────────────────────────────────────

Section::make('Selected Add-ons')
->schema([

    TextEntry::make('selected_addons_display')
        ->label('Add-ons')
        ->state(function ($record) {

            $addOns = $record->addOns;

            if ($addOns->isEmpty()) {
                return 'No add-ons selected.';
            }

            return $addOns
                ->unique('id')
                ->map(function ($addOn) {

                    $price = $addOn->pivot?->price_at_booking
                        ?? $addOn->price;

                    return e($addOn->name)
                        . ' — ₦'
                        . number_format((float) $price, 2);
                })
                ->implode('<br>');

        })
        ->html()
        ->columnSpanFull(),

])
->visible(fn ($record) => $record->addOns->isNotEmpty()),


                // ─────────────────────────────────────────────
                // TIMESTAMPS
                // ─────────────────────────────────────────────
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}