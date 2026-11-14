<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Models\Article;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Article $record) => '/blog/' . $record->slug),

                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->state(fn (Article $record) => match (true) {
                        $record->published_at === null => 'Brouillon',
                        $record->published_at->isFuture() => 'Programmé',
                        default => 'Publié',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Publié' => 'success',
                        'Programmé' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('published_at')
                    ->label('Publication')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'published' => 'Publié',
                        'scheduled' => 'Programmé',
                        'draft' => 'Brouillon',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'published' => $query->published(),
                        'scheduled' => $query->where('published_at', '>', now()),
                        'draft' => $query->whereNull('published_at'),
                        default => $query,
                    }),

                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(fn () => Article::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category', 'category')->all()),
            ])
            ->recordActions([
                Action::make('voir')
                    ->label('Voir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Article $record) => route('blog.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Article $record) => $record->published_at?->isPast()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
