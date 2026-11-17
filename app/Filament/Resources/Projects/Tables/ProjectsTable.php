<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(fn (Action $action, bool $isReordering) => $action
                ->label($isReordering ? 'Terminer' : 'Réordonner'))
            ->modifyQueryUsing(fn ($query) => $query->with('technologies')->withCount(['features', 'images']))
            ->columns([
                ImageColumn::make('main_image')
                    ->label('')
                    ->state(fn (Project $record) => $record->main_image_url)
                    ->imageHeight(48),

                TextColumn::make('title')
                    ->label('Projet')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Project $record) => collect([$record->date_label, $record->context])->filter()->join(' · ')),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'web' ? 'Web' : 'Graphisme')
                    ->color(fn (string $state) => $state === 'web' ? 'info' : 'warning'),

                ToggleColumn::make('show_on_lovaweb')
                    ->label('Lovaweb')
                    ->disabled(fn (Project $record) => $record->type !== 'web')
                    ->tooltip(fn (Project $record) => $record->type !== 'web' ? 'Lovaweb n\'affiche que les projets web' : null),

                ToggleColumn::make('is_featured')
                    ->label('Accueil Lovaweb')
                    ->disabled(fn (Project $record) => $record->type !== 'web' || ! $record->show_on_lovaweb)
                    ->toggleable(),

                ToggleColumn::make('show_on_portfolio')
                    ->label('Portfolio'),

                ToggleColumn::make('is_featured_portfolio')
                    ->label('Avant portfolio')
                    ->disabled(fn (Project $record) => ! $record->show_on_portfolio)
                    ->toggleable(),

                TextColumn::make('features_count')
                    ->label('Caract.')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('images_count')
                    ->label('Images')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('technologies.name')
                    ->label('Technologies')
                    ->badge()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(Project::TYPES),
                TernaryFilter::make('show_on_lovaweb')
                    ->label('Visible sur Lovaweb'),
                TernaryFilter::make('show_on_portfolio')
                    ->label('Visible sur le portfolio'),
                SelectFilter::make('technologies')
                    ->label('Technologie')
                    ->relationship('technologies', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('page')
                    ->label('Page')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Project $record) => route('projets.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Project $record) => $record->type === 'web' && $record->show_on_lovaweb),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('showPortfolio')
                        ->label('Afficher sur le portfolio')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each->update(['show_on_portfolio' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('hidePortfolio')
                        ->label('Masquer du portfolio')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['show_on_portfolio' => false]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('showLovaweb')
                        ->label('Afficher sur Lovaweb')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->where('type', 'web')->each->update(['show_on_lovaweb' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('hideLovaweb')
                        ->label('Masquer de Lovaweb')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['show_on_lovaweb' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
