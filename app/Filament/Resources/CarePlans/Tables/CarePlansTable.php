<?php

namespace App\Filament\Resources\CarePlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarePlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('careType.name')
                    ->label('Care Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->money('NGN')
                    ->sortable(),

                TextColumn::make('billing_cycle')
                    ->label('Billing Cycle')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'per_day'   => 'Per Day',
                        'per_night' => 'Per Night',
                        'per_24h'   => 'Per 24 Hours',
                        'per_week'  => 'Per Week',
                        'per_month' => 'Per Month',
                        'flat'      => 'One-Time',
                        default     => '-',
                    })
                    ->sortable(),

                TextColumn::make('meta.duration_days')
                    ->label('Days'),

                TextColumn::make('meta.max_caregivers')
                    ->label('Max Caregivers'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('price')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}