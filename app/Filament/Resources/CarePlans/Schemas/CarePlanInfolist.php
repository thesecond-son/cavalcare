<?php

namespace App\Filament\Resources\CarePlans\Schemas;

use App\Models\CareType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CarePlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Care Plan Details ─────────────────────────────────────
                Section::make('Care Plan Details')
                    ->components([

                        TextEntry::make('name')
                            ->label('Plan Name'),

                        TextEntry::make('careType.name')
                            ->label('Care Type')
                            ->placeholder('-'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->placeholder('-'),

                        TextEntry::make('price')
                            ->label('Price')
                            ->money('NGN'),

                        TextEntry::make('billing_cycle')
                            ->label('Billing Cycle')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'per_day'   => 'Per Day',
                                'per_night' => 'Per Night',
                                'per_24h'   => 'Per 24 Hours',
                                'per_week'  => 'Per Week',
                                'per_month' => 'Per Month',
                                'flat'      => 'One-Time / Flat Fee',
                                default     => '-',
                            }),

                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('-')
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                // ── Plan Configuration ───────────────────────────────────
                Section::make('Plan Configuration')
                    ->components([

                        TextEntry::make('meta.duration_days')
                            ->label('Duration (Days)')
                            ->placeholder('-'),

                        TextEntry::make('meta.daily_hours')
                            ->label('Hours Per Day')
                            ->placeholder('-'),

                        TextEntry::make('meta.max_caregivers')
                            ->label('Maximum Caregivers')
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

                                return CareType::whereIn('id', $ids)
                                    ->pluck('name')
                                    ->join(', ') ?: '-';
                            })
                            ->columnSpanFull(),

                        TextEntry::make('meta.features')
                            ->label('Features')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode(' • ', $state);
                                }

                                return $state ?: '-';
                            })
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                // ── Timestamps ────────────────────────────────────────────
                Section::make('Timestamps')
                    ->components([

                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created'),

                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Last Updated'),

                    ])
                    ->columns(2),
            ]);
    }
}