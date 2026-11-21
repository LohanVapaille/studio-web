<?php

namespace App\Filament\Resources\LovaApps\Tables;

use App\Models\LovaApp;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LovaAppsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->disk('uploads')
                    ->square()
                    ->defaultImageUrl(fn (LovaApp $record) => 'data:image/svg+xml;utf8,' . rawurlencode(
                        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><rect width="40" height="40" rx="9" fill="' . e($record->accent_color) . '"/>'
                        . '<text x="20" y="26" font-family="Arial" font-size="16" font-weight="700" fill="#fff" text-anchor="middle">' . e($record->monogram) . '</text></svg>'
                    )),

                TextColumn::make('name')
                    ->label('Application')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (LovaApp $record) => $record->tagline),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => LovaApp::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => LovaApp::STATUS_COLORS[$state] ?? 'gray'),

                TextColumn::make('pricing_model')
                    ->label('Offre')
                    ->formatStateUsing(fn (string $state, LovaApp $record) => $record->price_label ?: (LovaApp::PRICING_MODELS[$state] ?? $state))
                    ->toggleable(),

                TextColumn::make('monthly_cost')
                    ->label('Coût / mois')
                    ->money('EUR', locale: 'fr')
                    ->placeholder('—')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('EUR', locale: 'fr')->label('Total')),

                TextColumn::make('mrr')
                    ->label('Revenu / mois')
                    ->money('EUR', locale: 'fr')
                    ->placeholder('—')
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->numeric(locale: 'fr')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_visible')
                    ->label('Visible'),

                ToggleColumn::make('is_featured')
                    ->label('En avant')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(LovaApp::STATUSES),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('')
                    ->tooltip("Ouvrir l'app")
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (LovaApp $record) => $record->url, shouldOpenInNewTab: true)
                    ->visible(fn (LovaApp $record) => filled($record->url)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucune application Lova')
            ->emptyStateDescription('Ajoutez LovaTrip, LovaTask, LovaCalendar… elles apparaîtront sur le site une fois rendues visibles.');
    }
}
