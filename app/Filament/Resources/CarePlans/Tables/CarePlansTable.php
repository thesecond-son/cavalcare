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

                TextColumn::make('service_level')
                    ->label('Level')
                    ->badge()
                    ->formatStateUsing(fn ($state) => \App\Models\CarePlan::SERVICE_LEVELS[$state] ?? $state)
                    ->color(fn ($state) => $state === 'nursing' ? 'info' : 'success')
                    ->sortable(),

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
                        'per_10_visits' => '10 Visits / Month',
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
                \Filament\Tables\Filters\SelectFilter::make('service_level')
                    ->label('Service Level')
                    ->options(\App\Models\CarePlan::SERVICE_LEVELS),
                \Filament\Tables\Filters\SelectFilter::make('care_type_id')
                    ->label('Care Type')
                    ->relationship('careType', 'name'),
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