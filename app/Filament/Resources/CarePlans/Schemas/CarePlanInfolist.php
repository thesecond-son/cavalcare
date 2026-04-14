<?php

namespace App\Filament\Resources\CarePlans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CarePlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Care Plan Details')
                ->components([
                    TextEntry::make('name')
                        ->label('Plan Name'),

                    TextEntry::make('price')
                        ->money('NGN')
                        ->label('Price'),

                    TextEntry::make('meta.duration_days')
                        ->label('Duration (Days)')
                        ->placeholder('-'),

                    TextEntry::make('meta.daily_hours')
                        ->label('Hours Per Day')
                        ->placeholder('-'),

                    TextEntry::make('meta.max_caregivers')
                        ->label('Max Caregivers')
                        ->placeholder('-'),

                    TextEntry::make('meta.is_long_term')
                        ->label('Long-Term Plan')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                        ->color(fn ($state) => $state ? 'success' : 'gray'),

                    TextEntry::make('meta.recommended_for')
                        ->label('Recommended Care Types')
                        ->formatStateUsing(function ($state) {
                            $ids = match (true) {
                                is_array($state) => $state,
                                is_numeric($state) => [$state],
                                default => [],
                            };

                            return \App\Models\CareType::whereIn('id', $ids)
                                ->pluck('name')
                                ->join(', ') ?: '-';
                        }),
                ])
                ->columns(2),

               Section::make('Timestamps')
                ->components([
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->label('Created'),

                    TextEntry::make('updated_at')
                        ->dateTime()
                        ->label('Last Updated'),
                ])
            ]);
    }
}
