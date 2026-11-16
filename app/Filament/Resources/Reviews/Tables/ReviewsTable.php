<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->disk('uploads')
                    ->circular()
                    ->defaultImageUrl(asset('img/logo_lova.jpg')),

                TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->description(fn (Review $record) => $record->role),

                TextColumn::make('quote')
                    ->label('Avis')
                    ->limit(90)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color('warning'),

                TextColumn::make('source')
                    ->label('Provenance')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'google' ? 'Google' : 'Direct')
                    ->color(fn (string $state) => $state === 'google' ? 'info' : 'gray'),

                ToggleColumn::make('is_published')
                    ->label('Visible'),

                TextColumn::make('reviewed_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
